<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Serializer\Construction;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Construction\ObjectConstructor;
use Solrphp\SolariumBundle\Tests\Stub\Model\ObjectOptional;
use Solrphp\SolariumBundle\Tests\Stub\Model\ObjectRequired;

/**
 * Object Constructor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ObjectConstructorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\UnknownTypeException
     * @throws \ReflectionException
     */
    public function testNonExistingClass()
    {
        $meta = new ClassMetadata('\\Foo\\Bar');
        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $context = DeserializationContext::create();

        self::assertNull((new ObjectConstructor())->construct($visitor, $meta, [], [], $context));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \ReflectionException
     */
    public function testNonRequiredArguments(): void
    {
        $meta = new ClassMetadata(ObjectOptional::class);
        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $context = DeserializationContext::create();

        $instance = (new ObjectConstructor())->construct($visitor, $meta, [], [], $context);
        self::assertInstanceOf(ObjectOptional::class, $instance);

        // should be allowed:
        $instance->getFoo();
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \ReflectionException
     */
    public function testRequiredArguments(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Solrphp\SolariumBundle\Tests\Stub\Model\ObjectRequired::$foo must not be accessed before initialization');

        $meta = new ClassMetadata(ObjectRequired::class);
        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $context = DeserializationContext::create();

        $instance = (new ObjectConstructor())->construct($visitor, $meta, [], [], $context);
        self::assertInstanceOf(ObjectRequired::class, $instance);

        $instance->getFoo();
    }
}
