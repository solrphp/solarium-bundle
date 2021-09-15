<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Cache;
use Solrphp\SolariumBundle\SolrApi\Config\CopyField;
use Solrphp\SolariumBundle\SolrApi\Config\FieldType;
use Solrphp\SolariumBundle\SolrApi\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Config\Property;
use Solrphp\SolariumBundle\SolrApi\Config\Query;
use Solrphp\SolariumBundle\SolrApi\Config\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Response\CopyFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Response\DynamicFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Response\FieldsResponse;
use Solrphp\SolariumBundle\Tests\Util\ObjectUtil;

/**
 * Config Models Test.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class ConfigModelsTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param \JsonSerializable $class
     * @param bool              $populateNull
     */
    public function testSerialize(\JsonSerializable $class, bool $populateNull): void
    {
        $object = ObjectUtil::reflect($class, $populateNull);
        $properties = ObjectUtil::properties($class, $populateNull);
        $serialized = $object->jsonSerialize();

        foreach ($properties as $name => $getter) {
            // normalized array keys
            $normalised = !isset($serialized[$name]) ? strtolower(preg_replace('/(?<=[a-z])(?=[A-Z])/', '-', $name)) : $name;
            unset($properties[$name]);
            $properties[$normalised] = $getter;

            self::assertArrayHasKey($normalised, $serialized);

            if (\is_array($serialized[$normalised]) && \is_object($object->$getter()[0])) {
                self::assertSame($object->$getter()[0]->jsonSerialize(), $serialized[$normalised]);
            } else {
                self::assertSame($object->$getter(), $serialized[$normalised]);
            }
        }

        self::assertSame(array_keys($properties), array_keys($serialized));

        // some config models are completely nullable. in order to prevent tests from failing,
        // this rather useless assertion is made.
        self::assertIsArray($serialized);
    }

    /**
     * Test property serialization.
     */
    public function testPropertySerialization(): void
    {
        $field = new Property('foo', 'bar');

        self::assertSame(['foo' => 'bar'], $field->jsonSerialize());
        self::assertSame('foo', $field->getName());
        self::assertSame('bar', $field->getValue());
    }

    /**
     * Test empty request handler.
     */
    public function testEmptyRequestHandler(): void
    {
        $requestHandler = new RequestHandler('foo', 'bar');

        self::assertSame(['name' => 'foo', 'class' => 'bar'], $requestHandler->jsonSerialize());
    }

    /**
     * @dataProvider objectProvider
     *
     * test presence of property setters, so they can be used in object normalization.
     *
     * @param object $object
     * @param array  $unsets
     *
     * @see          vendor/symfony/serializer/Normalizer/ObjectNormalizer.php:165
     */
    public function testObjectGettersAndSetters(object $object, array $unsets): void
    {
        $getters = ObjectUtil::properties($object, true);
        $setters = ObjectUtil::properties($object, true, 'set');

        foreach ($unsets as $unset) {
            unset($setters[$unset]);
        }
        $populated = ObjectUtil::reflect($object);

        foreach ($getters as $property => $getter) {
            self::assertIsCallable([$object, $getter]);

            if (!isset($setters[$property])) {
                continue;
            }

            $value = $populated->$getter();
            $setter = $setters[$property];
            $object->$setter($value);

            self::assertSame($value, $object->$getter());
        }
    }

    /**
     * @dataProvider addersAndRemoversProvider
     *
     * @param object                $object
     * @param array<string, object> $properties
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAddersAndRemovers(object $object, array $properties): void
    {
        foreach ($properties as $name => $type) {
            [$adder, $remover, $setter, $getter] = ObjectUtil::methods($name);

            self::assertIsNotCallable([$object, $setter]);
            self::assertIsCallable([$object, $adder]);
            self::assertIsCallable([$object, $remover]);

            $clone = clone $type;
            $propVal = ObjectUtil::reflect($type);

            $object->$adder($propVal);
            $object->$remover($clone);

            self::assertContains($propVal, $object->$getter());

            $object->$remover($propVal);

            self::assertNotContains($propVal, $object->$getter());
        }
    }

    /**
     * @return \Generator
     */
    public function addersAndRemoversProvider(): \Generator
    {
        yield 'request_handler' => [
            new RequestHandler('foo', 'bar'),
            [
                'default' => new Property('foo', 'bar'),
                'append' => new Property('foo', 'bar'),
                'invariant' => new Property('foo', 'bar'),
            ],
        ];

        yield 'solr_config' => [
            new SolrConfig(new ArrayCollection()),
            [
                'searchComponent' => new SearchComponent('foo', 'bar'),
                'requestHandler' => new RequestHandler('foo', 'bar'),
            ],
        ];

        yield 'copy_fields_response' => [
            new CopyFieldsResponse(),
            [
                'copyField' => new CopyField(),
            ],
        ];

        yield 'dynamic_fields_response' => [
            new DynamicFieldsResponse(),
            [
                'dynamicField' => new FieldType(),
            ],
        ];

        yield 'fields_response' => [
            new FieldsResponse(),
            [
                'field' => new FieldType(),
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function objectProvider(): \Generator
    {
        yield 'field_type' => [
            'class' => new FieldType(),
            'unsets' => [],
        ];

        yield 'copy_field' => [
            'class' => new CopyField(),
            'unsets' => [],
        ];

        yield 'search_component' => [
            'class' => new SearchComponent('foo', 'bar'),
            'unsets' => [],
        ];

        yield 'request_handler' => [
            'class' => new RequestHandler('foo', 'bar'),
            'unsets' => [
                'defaults',
                'appends',
                'invariants',
            ],
        ];

        yield 'query' => [
            'class' => new Query(),
            'unsets' => [],
        ];

        yield 'cache' => [
            'class' => new Cache(),
            'unsets' => [],
        ];

        yield 'managed_schema' => [
            'class' => new ManagedSchema('foo'),
            'unsets' => [],
        ];

        yield 'property' => [
            'class' => new Property('foo', 'bar'),
            'unsets' => [],
        ];

        yield 'solr_config' => [
            'class' => new SolrConfig(new ArrayCollection(['foo'])),
            'unsets' => [
                'searchComponents',
                'requestHandlers',
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function dataProvider(): \Generator
    {
        yield 'request_handler_populated' => [
            'class' => new RequestHandler('foo', 'bar'),
            'populate_null' => true,
        ];

        yield 'cache_populated' => [
            'class' => new Cache(),
            'populate_null' => true,
        ];

        yield 'cache_skip_nullable' => [
            'class' => new Cache(),
            'populate_null' => false,
        ];

        yield 'copy_field_populated' => [
            'class' => new CopyField(),
            'populate_null' => true,
        ];

        yield 'copy_field_skip_nullable' => [
            'class' => new CopyField(),
            'populate_null' => false,
        ];

        yield 'field_type_populated' => [
            'class' => new FieldType(),
            'populate_null' => true,
        ];

        yield 'field_type_skip_nullable' => [
            'class' => new FieldType(),
            'populate_null' => false,
        ];

        yield 'query_populated' => [
            'class' => new Query(),
            'populate_null' => true,
        ];

        yield 'query_skip_nullable' => [
            'class' => new Query(),
            'populate_null' => false,
        ];

        yield 'request_handler_skip_nullable' => [
            'class' => new RequestHandler('foo', 'bar'),
            'populate_null' => false,
        ];

        yield 'search_component_populated' => [
            'class' => new SearchComponent('foo', 'bar'),
            'populate_null' => true,
        ];

        yield 'search_component_skip_nullable' => [
            'class' => new SearchComponent('foo', 'bar'),
            'populate_null' => false,
        ];
    }
}
