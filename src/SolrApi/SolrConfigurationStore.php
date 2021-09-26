<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi;

use JMS\Serializer\SerializerInterface;
use Solrphp\SolariumBundle\Common\Generator\LazyLoadingGenerator;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigGenerator;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaGenerator;

/**
 * Solr Configuration Store.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SolrConfigurationStore
{
    /**
     * @var LazyLoadingGenerator<\Generator<int, \Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface>>
     */
    private LazyLoadingGenerator $managedSchemas;

    /**
     * @var LazyLoadingGenerator<\Generator<int, \Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface>>
     */
    private LazyLoadingGenerator $solrConfigs;

    /**
     * @param array<int, array<string, ManagedSchema>> $managedSchemas
     * @param array<int, array<string, SolrConfig>>    $solrConfigs
     * @param \JMS\Serializer\SerializerInterface      $serializer
     *
     * @throws \JsonException
     */
    public function __construct(array $managedSchemas, array $solrConfigs, SerializerInterface $serializer)
    {
        $this->managedSchemas = new LazyLoadingGenerator((new SchemaGenerator($serializer))->generate($managedSchemas));
        $this->solrConfigs = new LazyLoadingGenerator((new ConfigGenerator($serializer))->generate($solrConfigs));
    }

    /**
     * @param string $core
     *
     * @return \Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema|null
     */
    public function getSchemaForCore(string $core): ?ManagedSchema
    {
        foreach ($this->managedSchemas as $schema) {
            if (true === \in_array($core, $schema->getCores()->toArray(), true)) {
                return $schema;
            }
        }

        return null;
    }

    /**
     * @param string $core
     *
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig|null
     */
    public function getConfigForCore(string $core): ?SolrConfig
    {
        foreach ($this->solrConfigs as $config) {
            if (true === \in_array($core, $config->getCores()->toArray(), true)) {
                return $config;
            }
        }

        return null;
    }
}
