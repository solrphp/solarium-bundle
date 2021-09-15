<?php

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$container->loadFromExtension('solrphp_solarium', [
    'endpoints' => [
        'default' => [
            'scheme' => 'https',
            'host' => '127.0.0.1',
            'port' => '8983',
            'path' => 'solr',
            'core' => 'demo',
            'collection' => 'demos',
        ],
    ],
    'default_client' => 'default',
    'clients' => [
        'default' => [
            'endpoints' => [
                'default',
            ],
            'default_endpoint' => 'default',
            'client_class' => 'Foo\Bar',
            'adapter_class' => 'Baz\Qux',
            'dispatcher_service' => 'custom.adapter',
        ],
    ],
    'managed_schemas' => [
        [
            'cores' => [
                'foo',
            ],
            'unique_key' => 'bar',
            'fields' => [
                'foo' => [
                    'name' => 'foo',
                    'type' => 'bar',
                    'class' => 'baz',
                    'position_increment_gap' => 1,
                    'auto_generate_phrase_queries' => true,
                    'synonym_query_style' => 'qux',
                    'enable_graph_queries' => true,
                    'doc_values_format' => 'foo',
                    'postings_format' => 'bar',
                    'sort_missing_first' => true,
                    'sort_missing_last' => false,
                    'multi_valued' => true,
                    'uninvertible' => true,
                    'omit_norms' => true,
                    'omit_term_freq_and_positions' => true,
                    'omit_positions' => true,
                    'term_vectors' => true,
                    'term_positions' => false,
                    'term_offsets' => true,
                    'term_payloads' => false,
                    'required' => false,
                    'use_doc_values_as_stored' => false,
                    'large' => false,
                    'doc_values' => false,
                    'indexed' => true,
                    'stored' => false,
                ],
            ],
            'copy_fields' => [
                [
                    'source' => 'foo',
                    'dest' => 'bar',
                    'max_chars' => 24,
                ],
            ],
            'dynamic_fields' => [
                '*_foo' => [
                    'name' => '*_foo',
                    'type' => 'bar',
                    'class' => 'baz',
                    'position_increment_gap' => 1,
                    'auto_generate_phrase_queries' => true,
                    'synonym_query_style' => 'qux',
                    'enable_graph_queries' => true,
                    'doc_values_format' => 'foo',
                    'postings_format' => 'bar',
                    'sort_missing_first' => true,
                    'sort_missing_last' => false,
                    'multi_valued' => true,
                    'uninvertible' => true,
                    'omit_norms' => true,
                    'omit_term_freq_and_positions' => true,
                    'omit_positions' => true,
                    'term_vectors' => true,
                    'term_positions' => false,
                    'term_offsets' => true,
                    'term_payloads' => false,
                    'required' => false,
                    'use_doc_values_as_stored' => false,
                    'large' => false,
                    'doc_values' => false,
                    'indexed' => true,
                    'stored' => false,
                ],
            ],
        ],
    ],
    'solr_configs' => [
        [
            'core' => [
                'foo',
            ],
            'search_components' => [
                'foo' => [
                    'name' => 'foo',
                    'class' => 'bar',
                ],
            ],
            'request_handlers' => [
                'foo' => [
                    'name' => 'foo',
                    'class' => 'bar',
                    'defaults' => [
                        [
                            'name' => 'baz',
                            'value' => 'qux',
                        ],
                    ],
                    'appends' => [
                        [
                            'name' => 'baz',
                            'value' => 'qux',
                        ],
                    ],
                    'invariants' => [
                        [
                            'name' => 'baz',
                            'value' => 'qux',
                        ],
                    ],
                    'components' => [],
                    'first_components' => [
                        'foo',
                    ],
                    'last_components' => [
                        'bar',
                    ],
                ],
            ],
            'query' => [
                'use_filter_for_sorted_query' => true,
                'query_result_window_size' => 10,
                'query_result_max_docs_cached' => 10,
                'enable_lazy_field_loading' => false,
                'max_boolean_clauses' => 10,
                'filter_cache' => [
                    'autowarm_count' => 10,
                    'size' => 10,
                    'initial_size' => 10,
                    'class' => 'foo',
                    'name' => 'bar',
                ],
                'query_result_cache' => [
                    'autowarm_count' => 10,
                    'size' => 10,
                    'initial_size' => 10,
                    'class' => 'foo',
                    'name' => 'bar',
                ],
                'document_cache' => [
                    'autowarm_count' => 10,
                    'size' => 10,
                    'initial_size' => 10,
                    'class' => 'foo',
                    'name' => 'bar',
                ],
                'field_value_cache' => [
                    'autowarm_count' => 10,
                    'size' => 10,
                    'initial_size' => 10,
                    'class' => 'foo',
                    'name' => 'bar',
                ],
            ],
        ],
    ],
]);
