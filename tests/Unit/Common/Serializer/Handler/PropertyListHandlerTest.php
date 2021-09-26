<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Serializer\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Handler\PropertyListHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;

/**
 * PropertyListHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PropertyListHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSubscribingMethods(): void
    {
        $subs = PropertyListHandler::getSubscribingMethods();

        self::assertSame(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, $subs[0]['direction']);
        self::assertSame('json', $subs[0]['format']);
        self::assertSame('PropertyList', $subs[0]['type']);
        self::assertSame(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, $subs[1]['direction']);
        self::assertSame('solr', $subs[1]['format']);
        self::assertSame('PropertyList', $subs[1]['type']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyArray(): void
    {
        $handler = new PropertyListHandler();
        $context = SerializationContext::create();
        $visitor = new JsonDeserializationVisitor();
        $data = [];

        self::assertEmpty($handler->deserializePropertyList($visitor, $data, [], $context));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConfigArray(): void
    {
        $handler = new PropertyListHandler();
        $context = SerializationContext::create();
        $visitor = new JsonDeserializationVisitor();

        $data = [
            [
                'name' => 'foo',
                'value' => 'bar',
            ],
        ];

        $result = $handler->deserializePropertyList($visitor, $data, [], $context);
        self::assertCount(1, $result);

        $property = $result[0];
        self::assertInstanceOf(Property::class, $property);
        self::assertSame('foo', $property->getName());
        self::assertSame('bar', $property->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testInvalidConfigArray(): void
    {
        $handler = new PropertyListHandler();
        $context = SerializationContext::create();
        $visitor = new JsonDeserializationVisitor();

        $data = [
            [
                'name' => 'foo',
            ],
        ];

        $result = $handler->deserializePropertyList($visitor, $data, [], $context);
        self::assertCount(0, $result);

        $data = [
            [
                'value' => 'foo',
            ],
        ];

        $result = $handler->deserializePropertyList($visitor, $data, [], $context);
        self::assertCount(0, $result);

        $data = [
            [
                'name' => 'foo',
                'value' => 1,
            ],
        ];

        $result = $handler->deserializePropertyList($visitor, $data, [], $context);
        self::assertCount(0, $result);

        $data = [
            [
                'name' => 'foo',
                'value' => 1,
            ],
            [
                'name' => 'foo',
                'value' => 'bar',
            ],
        ];

        $result = $handler->deserializePropertyList($visitor, $data, [], $context);
        self::assertCount(1, $result);

        $data = [
            'foo' => 1,
        ];

        $result = $handler->deserializePropertyList($visitor, $data, [], $context);
        self::assertCount(0, $result);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSolrArray(): void
    {
        $handler = new PropertyListHandler();
        $context = SerializationContext::create();
        $visitor = new JsonDeserializationVisitor();

        $data = [
            'foo' => 'bar',
        ];

        $result = $handler->deserializePropertyList($visitor, $data, [], $context);
        self::assertCount(1, $result);

        $property = $result[0];
        self::assertInstanceOf(Property::class, $property);
        self::assertSame('foo', $property->getName());
        self::assertSame('bar', $property->getValue());
    }
}
