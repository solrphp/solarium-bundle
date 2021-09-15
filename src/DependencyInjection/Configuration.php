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

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Client;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Solr Api Configuration.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('solrphp_solarium');
        $rootNode = $treeBuilder->getRootNode();

        $this->addEndpointsSection($rootNode);
        $this->addClientsSection($rootNode);
        $this->addManagedSchemasSection($rootNode);
        $this->addSolrConfigSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
     */
    private function addEndpointsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->fixXmlConfig('endpoint')
            ->children()
                ->arrayNode('endpoints')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('scheme')->end()
                            ->scalarNode('host')->end()
                            ->scalarNode('port')->end()
                            ->scalarNode('path')->end()
                            ->scalarNode('core')->end()
                            ->scalarNode('collection')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
     */
    private function addClientsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->fixXmlConfig('client')
            ->children()
                ->scalarNode('default_client')->cannotBeEmpty()->defaultValue('default')->end()
                ->arrayNode('clients')
                    ->canBeUnset()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->fixXmlConfig('endpoint')
                        ->validate()
                            ->ifTrue(static function ($v) {
                                return isset($v['adapter_class'], $v['adapter_service']);
                            })
                            ->then(static function ($v) {
                                $v['adapter_class'] = null;

                                return $v;
                            })
                        ->end()
                        ->children()
                            ->arrayNode('endpoints')
                                ->scalarPrototype()->end()
                            ->end()
                            ->scalarNode('default_endpoint')->end()
                            ->scalarNode('client_class')->defaultValue(Client::class)->end()
                            ->scalarNode('adapter_class')->defaultValue(Curl::class)->end()
                            ->scalarNode('adapter_service')
                                ->info('configuring an adapter service takes precedence over (default) client class configuration')
                            ->end()
                            ->scalarNode('dispatcher_service')->defaultValue('event_dispatcher')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
     */
    private function addSolrConfigSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->fixXmlConfig('solr_config')
            ->children()
                ->arrayNode('solr_configs')
                    ->arrayPrototype()
                        ->fixXmlConfig('core')
                        ->fixXmlConfig('search_component')
                        ->fixXmlConfig('request_handler')
                        ->validate()
                            ->ifTrue(static function ($v) {
                                return 0 === \count($v['cores'])
                                    && (0 !== \count($v['search_components']) || 0 !== \count($v['request_handlers']));
                            })
                            ->thenInvalid('define at least one core')
                        ->end()
                        ->validate()
                            ->ifTrue(static function ($v) {
                                return 0 === \count($v['search_components']);
                            })
                            ->then(static function ($v) {
                                unset($v['search_components']);

                                return $v;
                            })
                        ->end()
                        ->validate()
                            ->ifTrue(static function ($v) {
                                return 0 === \count($v['request_handlers']);
                            })
                            ->then(static function ($v) {
                                unset($v['request_handlers']);

                                return $v;
                            })
                        ->end()
                        ->children()
                            ->arrayNode('cores')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('search_components')
                                ->useAttributeAsKey('name', false)
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('name')->isRequired()->end()
                                        ->scalarNode('class')->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('request_handlers')
                                ->useAttributeAsKey('name', false)
                                ->arrayPrototype()
                                    ->validate()
                                        ->ifTrue(static function ($v) {
                                            return !empty($v['components']) && (!empty($v['last_components']) || !empty($v['first_components']));
                                        })
                                        ->thenInvalid('first/last components are only valid if you do not declare \'components\'')
                                    ->end()
                                    ->fixXmlConfig('default')
                                    ->fixXmlConfig('append')
                                    ->fixXmlConfig('invariant')
                                    ->fixXmlConfig('first_component')
                                    ->fixXmlConfig('last_component')
                                    ->children()
                                        ->scalarNode('name')->isRequired()->end()
                                        ->scalarNode('class')->isRequired()->end()
                                        ->arrayNode('defaults')
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('value')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('appends')
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('value')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('invariants')
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('value')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('components')
                                            ->scalarPrototype()->end()
                                        ->end()
                                        ->arrayNode('first_components')
                                            ->scalarPrototype()->end()
                                        ->end()
                                        ->arrayNode('last_components')
                                            ->scalarPrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('query')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('use_filter_for_sorted_query')->defaultFalse()->end()
                                    ->scalarNode('query_result_window_size')->defaultValue(20)->end()
                                    ->scalarNode('query_result_max_docs_cached')->defaultValue(200)->end()
                                    ->booleanNode('enable_lazy_field_loading')->defaultTrue()->end()
                                    ->scalarNode('max_boolean_clauses')->defaultValue(1024)->end()
                                    ->arrayNode('filter_cache')
                                        ->children()
                                            ->scalarNode('autowarm_count')->end()
                                            ->scalarNode('size')->end()
                                            ->scalarNode('initial_size')->end()
                                            ->scalarNode('class')->end()
                                            ->scalarNode('name')->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('query_result_cache')
                                        ->children()
                                            ->scalarNode('autowarm_count')->end()
                                            ->scalarNode('size')->end()
                                            ->scalarNode('initial_size')->end()
                                            ->scalarNode('class')->end()
                                            ->scalarNode('name')->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('document_cache')
                                        ->children()
                                            ->scalarNode('autowarm_count')->end()
                                            ->scalarNode('size')->end()
                                            ->scalarNode('initial_size')->end()
                                            ->scalarNode('class')->end()
                                            ->scalarNode('name')->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('field_value_cache')
                                        ->children()
                                            ->scalarNode('autowarm_count')->end()
                                            ->scalarNode('size')->end()
                                            ->scalarNode('initial_size')->end()
                                            ->scalarNode('class')->end()
                                            ->scalarNode('name')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
     *
     * @throws \RuntimeException
     */
    private function addManagedSchemasSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->fixXmlConfig('managed_schema')
            ->children()
                ->arrayNode('managed_schemas')
                    ->arrayPrototype()
                        ->fixXmlConfig('core')
                        ->fixXmlConfig('field')
                        ->fixXmlConfig('copy_field')
                        ->fixXmlConfig('dynamic_field')
                        ->children()
                            ->arrayNode('cores')
                                ->scalarPrototype()->end()
                            ->end()
                            ->scalarNode('unique_key')->isRequired()->end()
                            ->arrayNode('copy_fields')
                                ->fixXmlConfig('copy_field')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('source')->isRequired()->end()
                                        ->scalarNode('dest')->isRequired()->end()
                                        ->scalarNode('max_chars')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('fields')
                                ->fixXmlConfig('field')
                                ->useAttributeAsKey('name', false)
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('name')->isRequired()->end()
                                        ->scalarNode('type')->isRequired()->end()
                                        ->scalarNode('class')->end()
                                        ->scalarNode('position_increment_gap')->end()
                                        ->scalarNode('auto_generate_phrase_queries')->end()
                                        ->scalarNode('synonym_query_style')->end()
                                        ->scalarNode('enable_graph_queries')->end()
                                        ->scalarNode('doc_values_format')->end()
                                        ->scalarNode('postings_format')->end()
                                        ->scalarNode('indexed')->end()
                                        ->scalarNode('stored')->end()
                                        ->scalarNode('doc_values')->end()
                                        ->scalarNode('sort_missing_first')->end()
                                        ->scalarNode('sort_missing_last')->end()
                                        ->scalarNode('multi_valued')->end()
                                        ->scalarNode('uninvertible')->end()
                                        ->scalarNode('omit_norms')->end()
                                        ->scalarNode('omit_term_freq_and_positions')->end()
                                        ->scalarNode('omit_positions')->end()
                                        ->scalarNode('term_vectors')->end()
                                        ->scalarNode('term_positions')->end()
                                        ->scalarNode('term_offsets')->end()
                                        ->scalarNode('term_payloads')->end()
                                        ->scalarNode('required')->end()
                                        ->scalarNode('use_doc_values_as_stored')->end()
                                        ->scalarNode('large')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('dynamic_fields')
                                ->useAttributeAsKey('name', false)
                                ->fixXmlConfig('dynamic_field')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('name')
                                        ->isRequired()
                                            ->validate()
                                                ->ifTrue(static function ($v) {
                                                    return false === strpos($v, '*');
                                                })
                                                ->thenInvalid('a dynamic field name requires a wildcard %s')
                                            ->end()
                                        ->end()
                                        ->scalarNode('type')->isRequired()->end()
                                        ->scalarNode('class')->end()
                                        ->scalarNode('position_increment_gap')->end()
                                        ->scalarNode('auto_generate_phrase_queries')->end()
                                        ->scalarNode('synonym_query_style')->end()
                                        ->scalarNode('enable_graph_queries')->end()
                                        ->scalarNode('doc_values_format')->end()
                                        ->scalarNode('postings_format')->end()
                                        ->scalarNode('indexed')->end()
                                        ->scalarNode('stored')->end()
                                        ->scalarNode('doc_values')->end()
                                        ->scalarNode('sort_missing_first')->end()
                                        ->scalarNode('sort_missing_last')->end()
                                        ->scalarNode('multi_valued')->end()
                                        ->scalarNode('uninvertible')->end()
                                        ->scalarNode('omit_norms')->end()
                                        ->scalarNode('omit_term_freq_and_positions')->end()
                                        ->scalarNode('omit_positions')->end()
                                        ->scalarNode('term_vectors')->end()
                                        ->scalarNode('term_positions')->end()
                                        ->scalarNode('term_offsets')->end()
                                        ->scalarNode('term_payloads')->end()
                                        ->scalarNode('required')->end()
                                        ->scalarNode('use_doc_values_as_stored')->end()
                                        ->scalarNode('large')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
