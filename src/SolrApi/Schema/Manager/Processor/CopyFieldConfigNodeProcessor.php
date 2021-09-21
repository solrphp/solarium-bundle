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
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\CopyFieldsResponse;

/**
 * CopyField ConfigNode Processor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CopyFieldConfigNodeProcessor implements ConfigNodeProcessorInterface
{
    /**
     * @ var \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
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
            throw new ProcessorException(sprintf('unable to retrieve current field type config: %s', $e->getMessage()), $e);
        }

        if (!$current instanceof CopyFieldsResponse) {
            throw new ProcessorException(sprintf('invalid field type response for sub path %s', $configNode->getPath()));
        }

        try {
            $this->processConfigured($configNode, $current->getCopyFields());
            $this->processCurrent($configNode, $current->getCopyFields());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to add command for type %s: %s', $configNode->getType(), $e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return CopyField::class === $configNode->getType();
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return ConfigNodeProcessorInterface::PRIORITY;
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface $configNode
     * @param ArrayCollection<array-key, CopyField>|CopyField[]                      $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processConfigured(ConfigNodeInterface $configNode, ArrayCollection $current): void
    {
        foreach ($configNode->get() as $field) {
            if (true === $this->matching($current, $field)->isEmpty()) {
                $this->manager->addCommand(Command::ADD_COPY_FIELD, $field);
            }
        }
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface $configNode
     * @param ArrayCollection<array-key, CopyField>|CopyField[]                      $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processCurrent(ConfigNodeInterface $configNode, ArrayCollection $current): void
    {
        $configured = new ArrayCollection(iterator_to_array($configNode->get()));

        foreach ($current as $field) {
            if (true === $this->matching($configured, $field)->isEmpty()) {
                $this->manager->addCommand(Command::DELETE_COPY_FIELD, $field);
            }
        }
    }

    /**
     * @param ArrayCollection<array-key, CopyField>|CopyField[]      $collection
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField $field
     *
     * @return Collection<array-key, CopyField>|CopyField[]
     */
    private function matching(ArrayCollection $collection, CopyField $field): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('source', $field->getSource()))
            ->andWhere(Criteria::expr()->eq('dest', $field->getDest()));

        return $collection->matching($criteria);
    }
}
