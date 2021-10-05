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
use Solrphp\SolariumBundle\Common\Exception\ProcessorException;
use Solrphp\SolariumBundle\Common\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldTypeResponse;

/**
 * FieldType ConfigNode Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldTypeConfigNodeHandler implements ConfigNodeHandlerInterface
{
    /**
     * @ var \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
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
            throw new ProcessorException(sprintf('unable to retrieve current field type config: %s', $e->getMessage()), $e);
        }

        if (!$current instanceof FieldTypeResponse) {
            throw new ProcessorException(sprintf('invalid field type response for sub path %s', $configNode->getPath()));
        }

        try {
            $this->processConfigured($configNode, $current->getFieldTypes());
            $this->processCurrent($configNode, $current->getFieldTypes());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to add command for type %s: %s', $configNode->getType(), $e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return FieldType::class === $configNode->getType();
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return ConfigNodeHandlerInterface::PRIORITY;
    }

    /**
     * @param ArrayCollection<array-key, FieldType>                  $collection
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType $field
     *
     * @return Collection|FieldType[]
     */
    private function matching(ArrayCollection $collection, FieldType $field): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('name', $field->getName()));

        return $collection->matching($criteria);
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Manager\IterableConfigNode $configNode
     * @param ArrayCollection<array-key, FieldType>                     $current
     *
     * @throws \Solrphp\SolariumBundle\Common\Exception\UnexpectedValueException
     */
    private function processConfigured(IterableConfigNode $configNode, ArrayCollection $current): void
    {
        foreach ($configNode->get() as $field) {
            $command = $this->matching($current, $field)->isEmpty() ? Command::ADD_FIELD_TYPE : Command::REPLACE_FIELD_TYPE;
            $this->manager->addCommand($command, $field);
        }
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Manager\IterableConfigNode $configNode
     * @param ArrayCollection<array-key, FieldType>                     $current
     *
     * @throws \Solrphp\SolariumBundle\Common\Exception\UnexpectedValueException
     */
    private function processCurrent(IterableConfigNode $configNode, ArrayCollection $current): void
    {
        $configured = new ArrayCollection(iterator_to_array($configNode->get()));

        foreach ($current as $field) {
            if ($this->matching($configured, $field)->isEmpty()) {
                $this->manager->addCommand(Command::DELETE_FIELD_TYPE, new FieldType($field->getName()));
            }
        }
    }
}
