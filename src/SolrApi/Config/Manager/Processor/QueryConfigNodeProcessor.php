<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Manager\Processor;

use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Config\Util\ConfigUtil;

/**
 * Query ConfigNode Processor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class QueryConfigNodeProcessor implements ConfigNodeProcessorInterface
{
    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
     */
    private SolrApiManagerInterface $manager;

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface $manager
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface
     */
    public function setManager(SolrApiManagerInterface $manager): ConfigNodeProcessorInterface
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Query settings are considered common properties so in order
     * to update them, we need to construct their property paths
     * prior to setting / updating each individual property.
     *
     * @see https://lucene.apache.org/solr/guide/config-api.html#commands-for-common-properties
     *
     * {@inheritdoc}
     */
    public function process(ConfigNodeInterface $configNode): void
    {
        try {
            $current = $this->manager->call($configNode->getPath());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to retrieve query config for sub path %s', $configNode->getPath()), $e);
        }

        if (!$current instanceof ConfigResponse) {
            throw new ProcessorException(sprintf('invalid query response for sub path %s', $configNode->getPath()));
        }

        // should be only one
        foreach ($configNode->get() as $query) {
            $configured = null !== $query ? ConfigUtil::toPropertyPaths($query, 'query') : [];
            $actual = null !== $current->getConfig()->getQuery() ? ConfigUtil::toPropertyPaths($current->getConfig()->getQuery(), 'query') : [];

            $updates = array_diff_assoc($configured, $actual);

            foreach ($updates as $name => $value) {
                $command = (null === $value) ? Command::UNSET_PROPERTY : Command::SET_PROPERTY;

                try {
                    $this->manager->addCommand($command, new Property($name, $value));
                } catch (UnexpectedValueException $e) {
                    throw new ProcessorException(sprintf('unable to %s %s with value %s', $command, $name, $value ?? '[null]'), $e);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return Query::class === $configNode->getType();
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return ConfigNodeProcessorInterface::PRIORITY;
    }
}
