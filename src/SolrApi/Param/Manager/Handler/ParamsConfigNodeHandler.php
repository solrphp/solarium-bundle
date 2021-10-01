<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Manager\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;

/**
 * Params ConfigNode Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamsConfigNodeHandler implements ConfigNodeHandlerInterface
{
    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
     */
    private SolrApiManagerInterface $manager;

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
            throw new ProcessorException('unable to retrieve params config from sub path', $e);
        }

        if (!$current instanceof ParamResponse) {
            throw new ProcessorException('invalid params response for sub path');
        }

        try {
            $this->processConfigured($configNode, $current->getParams());
            $this->processCurrent($configNode, $current->getParams());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to add command for type %s: %s', $configNode->getType(), $e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return ParameterSetMap::class === $configNode->getType();
    }

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
    public static function getDefaultPriority(): int
    {
        return ConfigNodeHandlerInterface::PRIORITY;
    }

    /**
     * @param ArrayCollection<array-key, ParameterSetMap>|ParameterSetMap[] $collection
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap   $field
     *
     * @return Collection|ParameterSetMap[]
     */
    private function matching(ArrayCollection $collection, $field): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('name', $field->getName()));

        return $collection->matching($criteria);
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Manager\IterableConfigNode     $configNode
     * @param \Doctrine\Common\Collections\ArrayCollection<ParameterSetMap> $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processConfigured(IterableConfigNode $configNode, ArrayCollection $current): void
    {
        foreach ($configNode->get() as $param) {
            $command = $this->matching($current, $param)->isEmpty() ? Command::SET_PARAM : Command::UPDATE_PARAM;
            $this->manager->addCommand($command, $param);
        }
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Manager\IterableConfigNode $configNode
     * @param ArrayCollection<array-key, ParameterSetMap>               $current
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    private function processCurrent(IterableConfigNode $configNode, ArrayCollection $current): void
    {
        $configured = new ArrayCollection(iterator_to_array($configNode->get()));

        foreach ($current as $field) {
            if ($this->matching($configured, $field)->isEmpty()) {
                $this->manager->addCommand(Command::DELETE_PARAM, new ParameterSetMap($field->getName()));
            }
        }
    }
}
