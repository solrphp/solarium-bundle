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

use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Config\Util\ConfigUtil;

/**
 * RequestDispatcher ConfigNode Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestDispatcherConfigNodeHandler implements ConfigNodeHandlerInterface
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
     * request parser settings are considered common properties so in order
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
            throw new ProcessorException(sprintf('unable to retrieve request dispatcher config for sub path %s', $configNode->getPath()), $e);
        }

        if (!$current instanceof ConfigResponse) {
            throw new ProcessorException(sprintf('invalid request dispatcher response for sub path %s', $configNode->getPath()));
        }

        $configured = ConfigUtil::toPropertyPaths($configNode->get(), 'requestDispatcher');
        $actual = null !== $current->getConfig()->getRequestDispatcher() ? ConfigUtil::toPropertyPaths($current->getConfig()->getRequestDispatcher(), 'requestDispatcher') : [];

        $this->processValues($configured, Command::SET_PROPERTY);
        $this->processValues(array_diff_key($actual, $configured), Command::UNSET_PROPERTY);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return RequestDispatcher::class === $configNode->getType();
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return ConfigNodeHandlerInterface::PRIORITY;
    }

    /**
     * @param array<string, string|null> $values
     * @param string                     $command
     *
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    private function processValues(array $values, string $command): void
    {
        foreach ($values as $name => $value) {
            try {
                $this->manager->addCommand($command, new Property($name, $value));
            } catch (UnexpectedValueException $e) {
                throw new ProcessorException(sprintf('unable to %s %s', $command, $name), $e);
            }
        }
    }
}
