<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\DependencyInjection;

use Solarium\Core\Client\Endpoint;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Solr Api Extension.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrphpSolariumExtension extends Extension
{
    /**
     * @param array<string|int, mixed>                                $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return \Solrphp\SolariumBundle\DependencyInjection\Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        // no service definitions for now
        // $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        // $loader->load('services.yaml');

        $endpointReferences = $this->loadSolrEndpoints($container, $config);

        $this->loadSolrClients($container, $config, $endpointReferences);

        $this->loadSolrConfigurationStore($container, $config);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array<string, mixed>                                    $config
     */
    private function loadSolrConfigurationStore(ContainerBuilder $container, array $config): void
    {
        $definition = new Definition(SolrConfigurationStore::class, [
            $config['managed_schemas'],
            $config['solr_configs'],
        ]);

        $container->setDefinition(SolrConfigurationStore::class, $definition);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array<string, mixed>                                    $config
     *
     * @return array<int|string, Reference>
     */
    private function loadSolrEndpoints(ContainerBuilder $container, array $config): array
    {
        $endpointReferences = [];

        foreach ($config['endpoints'] as $name => $endpointOptions) {
            $endpointName = sprintf('solarium.client.endpoint.%s', $name);
            $endpointOptions['key'] = $name;

            $container
                ->setDefinition($endpointName, new Definition(Endpoint::class))
                ->setArguments([$endpointOptions]);

            $endpointReferences[$name] = new Reference($endpointName);
        }

        return $endpointReferences;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array<string, mixed>                                    $config
     * @param array<int|string, Reference>                            $endpointReferences
     */
    private function loadSolrClients(ContainerBuilder $container, array $config, array $endpointReferences): void
    {
        $defaultClient = $config['default_client'];

//        if (!count($config['clients'])) {
//            $config['clients'][$defaultClient] = [];
//        } elseif (count($config['clients']) === 1) {
//            $defaultClient = key($config['clients']);
//        }

        foreach ($config['clients'] as $name => $clientOptions) {
            $clientName = sprintf('solarium.client.%s', $name);

            $clientDefinition = new Definition($clientOptions['client_class']);
            $clients[$name] = new Reference($clientName);

            $container->setDefinition($clientName, $clientDefinition);

            if ($name === $defaultClient) {
                $container->setAlias('solarium.client', new Alias($clientName, true));
                $container->setAlias($clientOptions['client_class'], new Alias($clientName, true));
            }

            $adapterName = $clientOptions['adapter_service'] ?? sprintf('solarium.adapter.%s', $name);

            if (!isset($clientOptions['adapter_service']) && !$container->hasDefinition($adapterName)) {
                $container->register($adapterName, $clientOptions['adapter_class']);
            }

            $endpoints = array_intersect_key($endpointReferences, array_flip($clientOptions['endpoints'])) ?: $endpointReferences;

            $clientDefinition->setArguments([
                new Reference($adapterName),
                new Reference($clientOptions['dispatcher_service']),
                [
                    'endpoints' => $endpoints,
                ],
            ]);

            if (isset($clientOptions['default_endpoint'], $endpointReferences[$clientOptions['default_endpoint']])) {
                $clientDefinition->addMethodCall('setDefaultEndpoint', [$clientOptions['default_endpoint']]);
            }
        }
    }
}
