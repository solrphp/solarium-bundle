<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Manager\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldsResponse;

/**
 * Field ConfigNode Processor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldConfigNodeProcessor implements ConfigNodeProcessorInterface
{
    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
     */
    private SolrApiManagerInterface $manager;

    /**
     * {@inheritdoc}
     */
    public function setManager(SolrApiManagerInterface $manager): ConfigNodeProcessorInterface
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConfigNodeInterface $configNode): void
    {
        try {
            $current = $this->manager->call($configNode->getPath());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to retrieve current field config: %s', $e->getMessage()), $e);
        }

        if (!$current instanceof FieldsResponse) {
            throw new ProcessorException(sprintf('invalid field response for sub path %s', $configNode->getPath()));
        }

        try {
            $this->processConfigured($configNode, $current->getFields());
            $this->processCurrent($configNode, $current->getFields());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to add command for type %s: %s', $configNode->getType(), $e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return Field::class === $configNode->getType() && SubPath::LIST_FIELDS === $configNode->getPath();
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return ConfigNodeProcessorInterface::PRIORITY;
    }

    /**
     * @param ArrayCollection<array-key, Field>                  $collection
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $field
     *
     * @return Collection<array-key, Field>|Field[]
     */
    private function matching(ArrayCollection $collection, Field $field): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('name', $field->getName()));

        return $collection->matching($criteria);
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface $configNode
     * @param ArrayCollection<array-key, Field>                                      $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processConfigured(ConfigNodeInterface $configNode, ArrayCollection $current): void
    {
        foreach ($configNode->get() as $field) {
            $command = $this->matching($current, $field)->isEmpty() ? Command::ADD_FIELD : Command::REPLACE_FIELD;
            $this->manager->addCommand($command, $field);
        }
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface $configNode
     * @param ArrayCollection<array-key, Field>                                      $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processCurrent(ConfigNodeInterface $configNode, ArrayCollection $current): void
    {
        $configured = new ArrayCollection(iterator_to_array($configNode->get()));

        foreach ($current as $field) {
            if ($this->matching($configured, $field)->isEmpty()) {
                $this->manager->addCommand(Command::DELETE_FIELD, new Field($field->getName()));
            }
        }
    }
}
