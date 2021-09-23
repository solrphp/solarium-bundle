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
use Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Config\Util\ConfigUtil;

/**
 * Query ConfigNode Processor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UpdateHandlerConfigNodeProcessor implements ConfigNodeProcessorInterface
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
     * update handler settings are considered common properties so in order
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

        $configured = ConfigUtil::toPropertyPaths($configNode->get(), 'updateHandler');
        $actual = null !== $current->getConfig()->getUpdateHandler() ? ConfigUtil::toPropertyPaths($current->getConfig()->getUpdateHandler(), 'updateHandler') : [];

        $this->processValues($configured, Command::SET_PROPERTY);
        $this->processValues(array_diff_key($actual, $configured), Command::UNSET_PROPERTY);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ConfigNodeInterface $configNode): bool
    {
        return UpdateHandler::class === $configNode->getType();
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return ConfigNodeProcessorInterface::PRIORITY;
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
