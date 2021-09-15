<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Client;
use Solrphp\SolariumBundle\DependencyInjection\Configuration;
use Solrphp\SolariumBundle\DependencyInjection\SolrphpSolariumExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * SolrApi Configuration Test.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class SolrApiConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * test empty configuration.
     */
    public function testEmptyConfiguration(): void
    {
        // required parameters for the configuration store
        $expectedConfiguration = [
            'default_client' => 'default',
            'endpoints' => [],
            'clients' => [],
            'managed_schemas' => [],
            'solr_configs' => [],
        ];

        $formats = array_map(
            static function ($path) {
                return __DIR__.'/../../Stub/'.$path;
            },
            [
                'config/empty.yaml',
                'config/empty.xml',
                'config/empty.php',
            ]
        );

        foreach ($formats as $format) {
            $this->assertProcessedConfigurationEquals($expectedConfiguration, [$format]);
        }
    }

    /**
     * test full config.
     */
    public function testFullConfig(): void
    {
        $expectedConfiguration = [
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
                    'cores' => [
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
        ];

        $formats = array_map(
            static function ($path) {
                return __DIR__.'/../../Stub/'.$path;
            },
            [
                'config/full.yaml',
                'config/full.php',
                'config/full.xml',
            ]
        );

        foreach ($formats as $format) {
            $this->assertProcessedConfigurationEquals($expectedConfiguration, [$format]);
        }
    }

    /**
     * test invalid search component config.
     */
    public function testInvalidSearchComponentConfig(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('define at least one core');

        $config = [
            'solr_configs' => [
                [
                    'cores' => [],
                    'search_components' => [
                        [
                            'name' => 'foo',
                            'class' => 'bar',
                        ],
                    ],
                ],
            ],
        ];

        (new Processor())->processConfiguration($this->getConfiguration(), ['solrphp_solarium' => $config]);
    }

    /**
     * test invalid request handler config.
     */
    public function testInvalidRequestHandlerConfig(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('define at least one core');

        $config = [
            'solr_configs' => [
                [
                    'cores' => [],
                    'request_handlers' => [
                        [
                            'name' => 'foo',
                            'class' => 'bar',
                        ],
                    ],
                ],
            ],
        ];

        (new Processor())->processConfiguration($this->getConfiguration(), ['solrphp_solarium' => $config]);
    }

    /**
     * test last component exception.
     */
    public function testLastComponentException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('first/last components are only valid if you do not declare \'components\'');

        $config = [
            'solr_configs' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'request_handlers' => [
                        'foo' => [
                            'name' => 'foo',
                            'class' => 'bar',
                            'components' => [
                                'foo',
                            ],
                            'last_components' => [
                                'bar',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        (new Processor())->processConfiguration($this->getConfiguration(), ['solrphp_solarium' => $config]);
    }

    /**
     * test first component exception.
     */
    public function testFirstComponentException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('first/last components are only valid if you do not declare \'components\'');

        $config = [
            'solr_configs' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'request_handlers' => [
                        'foo' => [
                            'name' => 'foo',
                            'class' => 'bar',
                            'components' => [
                                'foo',
                            ],
                            'first_components' => [
                                'bar',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        (new Processor())->processConfiguration($this->getConfiguration(), ['solrphp_solarium' => $config]);
    }

    /**
     * test dynamic field exception.
     */
    public function testDynamicFieldException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('a dynamic field name requires a wildcard');

        $config = [
            'managed_schemas' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'unique_key' => 'bar',
                    'dynamic_fields' => [
                        'foo' => [
                            'name' => 'foo',
                            'type' => 'bar',
                            'class' => 'baz',
                        ],
                    ],
                ],
            ],
        ];

        (new Processor())->processConfiguration($this->getConfiguration(), ['solrphp_solarium' => $config]);
    }

    /**
     * test query default values.
     */
    public function testQueryDefaultValues(): void
    {
        $config = [
            'solr_configs' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'query' => [],
                ],
            ],
        ];

        $result = (new Processor())->processConfiguration($this->getConfiguration(), ['solrphp_solarium' => $config]);

        self::assertFalse($result['solr_configs'][0]['query']['use_filter_for_sorted_query']);
        self::assertSame(20, $result['solr_configs'][0]['query']['query_result_window_size']);
        self::assertSame(200, $result['solr_configs'][0]['query']['query_result_max_docs_cached']);
        self::assertTrue($result['solr_configs'][0]['query']['enable_lazy_field_loading']);
        self::assertSame(1024, $result['solr_configs'][0]['query']['max_boolean_clauses']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDefaultClientValues(): void
    {
        $config = [
            'clients' => [
                'default' => [],
            ],
        ];

        $configuration = new Configuration();
        $result = (new Processor())->processConfiguration($configuration, ['solrphp_solarium' => $config]);

        self::assertSame(Client::class, $result['clients']['default']['client_class']);
        self::assertSame(Curl::class, $result['clients']['default']['adapter_class']);
        self::assertSame('event_dispatcher', $result['clients']['default']['dispatcher_service']);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    protected function getContainerExtension(): ExtensionInterface
    {
        return new SolrphpSolariumExtension();
    }

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
