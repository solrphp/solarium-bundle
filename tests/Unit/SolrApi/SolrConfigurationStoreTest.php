<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter\ICUNormalizer2CharFilter;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\DoubleMetaphoneFilter;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\FlattenGraphFilter;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Tokenizer;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;

/**
 * Solr ConfigurationStore Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrConfigurationStoreTest extends TestCase
{
    /**
     * @dataProvider configProvider
     *
     * @param array  $configs
     * @param string $coreConfig
     * @param int    $searchComponentCount
     * @param int    $requestHandlerCount
     * @param bool   $queryObject
     * @param bool   $handlerObject
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConfigInitialization(array $configs, string $coreConfig, int $searchComponentCount, int $requestHandlerCount, bool $queryObject, bool $handlerObject): void
    {
        $config = (new SolrConfigurationStore([], $configs))
            ->getConfigForCore($coreConfig);

        self::assertInstanceOf(SolrConfig::class, $config);
        self::assertCount($searchComponentCount, $config->getSearchComponents());
        self::assertCount($requestHandlerCount, $config->getRequestHandlers());
        self::assertSame($queryObject, \is_object($config->getQuery()));
        self::assertSame($handlerObject, \is_object($config->getUpdateHandler()));

        if (0 !== $searchComponentCount) {
            self::assertInstanceOf(SearchComponent::class, $config->getSearchComponents()[0]);
        }

        if (0 !== $requestHandlerCount) {
            self::assertInstanceOf(RequestHandler::class, $config->getRequestHandlers()[0]);
        }
    }

    /**
     * @dataProvider schemaProvider
     *
     * @param array  $schemas
     * @param string $coreConfig
     * @param string $key
     * @param int    $fieldCount
     * @param int    $dynamicFieldCount
     * @param int    $copyFieldCount
     * @param int    $fieldTypeCount
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testManagedSchemaInitialization(array $schemas, string $coreConfig, string $key, int $fieldCount, int $dynamicFieldCount, int $copyFieldCount, int $fieldTypeCount): void
    {
        $schema = (new SolrConfigurationStore($schemas, []))
            ->getSchemaForCore($coreConfig);

        self::assertInstanceOf(ManagedSchema::class, $schema);
        self::assertCount($fieldCount, $schema->getFields());
        self::assertCount($dynamicFieldCount, $schema->getDynamicFields());
        self::assertCount($copyFieldCount, $schema->getCopyFields());
        self::assertCount($fieldTypeCount, $schema->getFieldTypes());
        self::assertSame($key, $schema->getUniqueKey());

        if (0 !== $fieldCount) {
            self::assertInstanceOf(Field::class, $schema->getFields()[0]);
        }

        if (0 !== $dynamicFieldCount) {
            self::assertInstanceOf(Field::class, $schema->getDynamicFields()[0]);
        }

        if (0 !== $copyFieldCount) {
            self::assertInstanceOf(CopyField::class, $schema->getCopyFields()[0]);
        }

        if (0 !== $fieldTypeCount) {
            self::assertInstanceOf(FieldType::class, $schema->getFieldTypes()[0]);
        }
    }

    /**
     * make sure property name normalization and array denormalization is properly done.
     */
    public function testConfigNormalization(): void
    {
        $config = [
            'cores' => [
                'foo',
            ],
            'search_components' => [],
            'request_handlers' => [
                [
                    'name' => 'foo',
                    'class' => 'bar',
                    'last_components' => [
                        'baz',
                        'qux',
                    ],
                    'invariants' => [
                        ['name' => 'foo', 'value' => 'bar'],
                        ['name' => 'baz', 'value' => 'qux'],
                    ],
                ],
            ],
            'query' => null,
            'update_handler' => null,
        ];

        $config = (new SolrConfigurationStore([], [$config]))
            ->getConfigForCore('foo');

        $handler = $config->getRequestHandlers()[0];
        self::assertCount(2, $handler->getLastComponents());
        self::assertCount(2, $handler->getInvariants());
        self::assertInstanceOf(Property::class, $handler->getInvariants()[0]);
    }

    /**
     * make sure property name normalization and array denormalization is properly done.
     */
    public function testSchemaNormalization(): void
    {
        $schema = [
            'cores' => [
                'foo',
            ],
            'fields' => [
                ['name' => 'foo', 'type' => 'bar', 'term_payloads' => false],
            ],
            'dynamic_fields' => [
                ['name' => 'foo', 'class' => 'bar', 'term_vectors' => true],
            ],
            'copy_fields' => [
                ['source' => 'foo', 'dest' => 'bar', 'max_chars' => 24],
            ],
            'field_types' => [
                ['name' => 'foo', 'class' => 'bar'],
            ],
            'unique_key' => 'foo',
        ];

        $schema = (new SolrConfigurationStore([$schema], []))
            ->getSchemaForCore('foo');

        self::assertFalse($schema->getFields()[0]->getTermPayloads());
        self::assertTrue($schema->getDynamicFields()[0]->getTermVectors());
        self::assertSame(24, $schema->getCopyFields()[0]->getMaxChars());
        self::assertSame('foo', $schema->getFieldTypes()[0]->getName());
    }

    /**
     * test object nesting and discriminator deserialization.
     */
    public function testObjectNestingDeserializationAndDiscriminator(): void
    {
        $schema = [
            'cores' => [
                'foo',
            ],
            'fields' => [],
            'dynamic_fields' => [],
            'copy_fields' => [],
            'field_types' => [
                [
                    'name' => 'foo',
                    'class' => 'bar',
                    'analyzers' => [
                        [
                            'class' => 'foo',
                            'type' => 'bar',
                            'char_filters' => [
                                [
                                    'class' => 'solr.ICUNormalizer2CharFilterFactory',
                                    'name' => 'foo',
                                ],
                            ],
                            'tokenizer' => [
                                'class' => 'foo',
                                'pattern' => 'bar',
                            ],
                            'filters' => [
                                [
                                    'class' => 'solr.DoubleMetaphoneFilterFactory',
                                    'inject' => false,
                                ],
                                [
                                    'class' => 'solr.FlattenGraphFilterFactory',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'unique_key' => 'foo',
        ];

        $schema = (new SolrConfigurationStore([$schema], []))
            ->getSchemaForCore('foo');

        /** @var FieldType $fieldType */
        $fieldType = $schema->getFieldTypes()[0];

        self::assertCount(1, $fieldType->getAnalyzers());

        $analyzer = $fieldType->getAnalyzers()[0];

        self::assertCount(1, $analyzer->getCharFilters());
        self::assertCount(2, $analyzer->getFilters());

        $charFilter = $analyzer->getCharFilters()[0];

        self::assertInstanceOf(ICUNormalizer2CharFilter::class, $charFilter);
        self::assertSame('foo', $charFilter->getName());

        $filter = $analyzer->getFilters()[0];
        self::assertInstanceOf(DoubleMetaphoneFilter::class, $filter);
        self::assertFalse($filter->isInject());

        $filter = $analyzer->getFilters()[1];
        self::assertInstanceOf(FlattenGraphFilter::class, $filter);

        self::assertInstanceOf(Tokenizer::class, $analyzer->getTokenizer());
    }

    /**
     * Test no configuration.
     */
    public function testNoConfigurations(): void
    {
        $store = new SolrConfigurationStore([], []);

        self::assertNull($store->getConfigForCore('foo'));
        self::assertNull($store->getSchemaForCore('foo'));
    }

    /**
     * @return \Generator
     */
    public function schemaProvider(): \Generator
    {
        yield 'full_schema' => [
            'schemas' => [
                [
                    'cores' => [
                        'foo',
                        'bar',
                    ],
                    'fields' => [
                        ['name' => 'foo', 'type' => 'bar', 'term_payloads' => false],
                        ['name' => 'baz', 'type' => 'qux'],
                    ],
                    'dynamic_fields' => [
                        ['name' => 'foo', 'class' => 'bar', 'term_vectors' => true],
                    ],
                    'copy_fields' => [
                        ['source' => 'foo', 'dest' => 'bar', 'max_chars' => 24],
                    ],
                    'field_types' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'unique_key' => 'foo',
                ],
            ],
            'core_config' => 'bar',
            'key' => 'foo',
            'fields_count' => 2,
            'dynamic_fields_count' => 1,
            'copy_fields_count' => 1,
            'field_type_count' => 1,
        ];

        yield 'one_core_one_field_schema' => [
            'schemas' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'fields' => [
                        ['name' => 'foo', 'type' => 'bar'],
                    ],
                    'dynamic_fields' => [],
                    'copy_fields' => [],
                    'field_types' => [],
                    'unique_key' => 'foo',
                ],
            ],
            'core_config' => 'foo',
            'key' => 'foo',
            'fields_count' => 1,
            'dynamic_fields_count' => 0,
            'copy_fields_count' => 0,
            'field_type_count' => 0,
        ];

        yield 'one_core_one_dynamic_field_schema' => [
            'schemas' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'fields' => [],
                    'dynamic_fields' => [
                        ['name' => 'foo', 'type' => 'bar'],
                    ],
                    'copy_fields' => [],
                    'field_types' => [],
                    'unique_key' => 'foo',
                ],
            ],
            'core_config' => 'foo',
            'key' => 'foo',
            'fields_count' => 0,
            'dynamic_fields_count' => 1,
            'copy_fields_count' => 0,
            'field_type_count' => 0,
        ];

        yield 'one_core_one_copy_field_schema' => [
            'schemas' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'fields' => [],
                    'dynamic_fields' => [],
                    'copy_fields' => [
                        ['source' => 'foo', 'dest' => 'bar'],
                    ],
                    'field_types' => [],
                    'unique_key' => 'foo',
                ],
            ],
            'core_config' => 'foo',
            'key' => 'foo',
            'fields_count' => 0,
            'dynamic_fields_count' => 0,
            'copy_fields_count' => 1,
            'field_type_count' => 0,
        ];

        yield 'one_core_one_field_type_schema' => [
            'schemas' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'fields' => [],
                    'dynamic_fields' => [],
                    'copy_fields' => [],
                    'field_types' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'unique_key' => 'foo',
                ],
            ],
            'core_config' => 'foo',
            'key' => 'foo',
            'fields_count' => 0,
            'dynamic_fields_count' => 0,
            'copy_fields_count' => 0,
            'field_type_count' => 1,
        ];

        yield 'multiple_schemas' => [
            'schemas' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'fields' => [],
                    'dynamic_fields' => [],
                    'copy_fields' => [
                        ['source' => 'foo', 'dest' => 'bar'],
                    ],
                    'field_types' => [],
                    'unique_key' => 'foo',
                ],
                [
                    'cores' => [
                        'bar',
                    ],
                    'fields' => [
                        ['name' => 'foo', 'type' => 'bar'],
                        ['name' => 'baz', 'type' => 'qux'],
                    ],
                    'dynamic_fields' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'copy_fields' => [
                        ['source' => 'foo', 'dest' => 'bar'],
                    ],
                    'field_types' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'unique_key' => 'baz',
                ],
            ],
            'core_config' => 'bar',
            'key' => 'baz',
            'fields_count' => 2,
            'dynamic_fields_count' => 1,
            'copy_fields_count' => 1,
            'field_type_count' => 1,
        ];
    }

    /**
     * @return \Generator
     */
    public function configProvider(): \Generator
    {
        yield 'full_config' => [
            'configs' => [
                [
                    'cores' => [
                        'foo',
                        'bar',
                    ],
                    'search_components' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'request_handlers' => [
                        ['name' => 'foo', 'class' => 'bar', 'last_components' => ['baz', 'qux']],
                    ],
                    'query' => [
                        'filterCache' => [
                            'name' => 'foo',
                        ],
                    ],
                    'update_handler' => [
                        'class' => 'foo',
                    ],
                    'request_dispatcher' => [
                        'handle_select' => false,
                    ],
                ],
            ],
            'core_config' => 'bar',
            'search_component_count' => 1,
            'request_handler_count' => 1,
            'query_is_object' => true,
            'update_handler_is_object' => true,
            'request_dispatcher_is_object' => true,
        ];

        yield 'one_core_one_component_config' => [
            'configs' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'search_components' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'request_handlers' => [],
                    'query' => null,
                    'update_handler' => null,
                    'request_dispatcher' => null,
                ],
            ],
            'core_config' => 'foo',
            'search_component_count' => 1,
            'request_handler_count' => 0,
            'query_is_object' => false,
            'update_handler_is_object' => false,
            'request_dispatcher_is_object' => false,
        ];

        yield 'one_core_one_handler_config' => [
            'configs' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'search_components' => [],
                    'request_handlers' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'query' => null,
                    'update_handler' => null,
                    'request_dispatcher' => null,
                ],
            ],
            'core_config' => 'foo',
            'search_component_count' => 0,
            'request_handler_count' => 1,
            'query_is_object' => false,
            'update_handler_is_object' => false,
            'request_dispatcher_is_object' => false,
        ];

        yield 'multiple_configs' => [
            'configs' => [
                [
                    'cores' => [
                        'foo',
                    ],
                    'search_components' => [],
                    'request_handlers' => [],
                    'query' => null,
                    'update_handler' => null,
                    'request_dispatcher' => null,
                ],
                [
                    'cores' => [
                        'bar',
                    ],
                    'search_components' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'request_handlers' => [
                        ['name' => 'foo', 'class' => 'bar'],
                    ],
                    'query' => null,
                ],
            ],
            'core_config' => 'bar',
            'search_component_count' => 1,
            'request_handler_count' => 1,
            'query_is_object' => false,
            'update_handler_is_object' => false,
            'request_dispatcher_is_object' => false,
        ];
    }
}
