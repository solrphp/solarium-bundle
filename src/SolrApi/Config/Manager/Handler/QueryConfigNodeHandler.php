<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Manager\Handler;

use Solrphp\SolariumBundle\Common\Exception\ProcessorException;
use Solrphp\SolariumBundle\Common\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Config\Util\ConfigUtil;

/**
 * Query ConfigNode Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class QueryConfigNodeHandler implements ConfigNodeHandlerInterface
{
    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
     */
    private SolrApiManagerInterface $manager;

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface $manager
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface
     */
    public function setManager(SolrApiManagerInterface $manager): ConfigNodeHandlerInterface
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
    public function handle(ConfigNodeInterface $configNode): void
    {
        try {
            $current = $this->manager->call($configNode->getPath());
        } catch (UnexpectedValueException $e) {
            throw new ProcessorException(sprintf('unable to retrieve query config for sub path %s', $configNode->getPath()), $e);
        }

        if (!$current instanceof ConfigResponse) {
            throw new ProcessorException(sprintf('invalid query response for sub path %s', $configNode->getPath()));
        }

        $configured = ConfigUtil::toPropertyPaths($configNode->get(), 'query');
        $actual = null !== $current->getConfig()->getQuery() ? ConfigUtil::toPropertyPaths($current->getConfig()->getQuery(), 'query') : [];

        $updates = array_diff_assoc($configured, $actual) + array_fill_keys(array_flip(array_diff_key($actual, $configured)), null);

        foreach ($updates as $name => $value) {
            try {
                $this->manager->addCommand($command = (null === $value) ? Command::UNSET_PROPERTY : Command::SET_PROPERTY, new Property($name, $value));
            } catch (UnexpectedValueException $e) {
                throw new ProcessorException(sprintf('unable to %s %s', $command ?? '[command not found]]', $name), $e);
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
        return ConfigNodeHandlerInterface::PRIORITY;
    }
}
