<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Manager\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\DynamicFieldsResponse;

/**
 * DynamicField ConfigNode Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class DynamicFieldConfigNodeHandler implements ConfigNodeHandlerInterface
{
    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
     */
    private SolrApiManagerInterface $manager;

    /**
     * {@inheritdoc}
     */
    public function setManager(SolrApiManagerInterface $manager): ConfigNodeHandlerInterface
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ConfigNodeInterface $configNode): void
    {
        if (!$configNode instanceof IterableConfigNode) {
            throw new ProcessorException(sprintf('invalid config node use %s', IterableConfigNode::class));
        }

        try {
            $current = $this->manager->call($configNode->getPath());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to retrieve current dynamic field config: %s', $e->getMessage()), $e);
        }

        if (!$current instanceof DynamicFieldsResponse) {
            throw new ProcessorException(sprintf('invalid dynamic field response for sub path %s', $configNode->getPath()));
        }

        try {
            $this->processConfigured($configNode, $current->getDynamicFields());
            $this->processCurrent($configNode, $current->getDynamicFields());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to add command for type %s: %s', $configNode->getType(), $e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return Field::class === $configNode->getType() && SubPath::LIST_DYNAMIC_FIELDS === $configNode->getPath();
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return ConfigNodeHandlerInterface::PRIORITY;
    }

    /**
     * @param ArrayCollection<array-key, Field>|Field[]          $collection
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $field
     *
     * @return Collection|Field[]
     */
    private function matching(ArrayCollection $collection, $field): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('name', $field->getName()));

        return $collection->matching($criteria);
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Manager\IterableConfigNode $configNode
     * @param \Doctrine\Common\Collections\ArrayCollection<Field>       $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processConfigured(IterableConfigNode $configNode, ArrayCollection $current): void
    {
        foreach ($configNode->get() as $field) {
            $command = $this->matching($current, $field)->isEmpty() ? Command::ADD_DYNAMIC_FIELD : Command::REPLACE_DYNAMIC_FIELD;
            $this->manager->addCommand($command, $field);
        }
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Manager\IterableConfigNode $configNode
     * @param ArrayCollection<array-key, Field>                         $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processCurrent(IterableConfigNode $configNode, ArrayCollection $current): void
    {
        $configured = new ArrayCollection(iterator_to_array($configNode->get()));

        foreach ($current as $field) {
            if ($this->matching($configured, $field)->isEmpty()) {
                $this->manager->addCommand(Command::DELETE_DYNAMIC_FIELD, new Field($field->getName()));
            }
        }
    }
}
