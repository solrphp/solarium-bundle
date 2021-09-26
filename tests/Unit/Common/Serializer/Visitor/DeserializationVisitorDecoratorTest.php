<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Serializer\Visitor;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Visitor\DeserializationVisitorDecorator;
use Solrphp\SolariumBundle\Common\Serializer\Visitor\PrepareCallable;

/**
 * DeserializationVisitorDecoratorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DeserializationVisitorDecoratorTest extends TestCase
{
    /**
     * @dataProvider provideMethods
     *
     * @param $method
     * @param $arguments
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function testPassThrough($method, $arguments): void
    {
        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $visitor->expects(self::once())->method($method)->with(...$arguments);

        $decorator = new DeserializationVisitorDecorator($visitor, \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']));

        $decorator->$method(...$arguments);
    }

    /**
     * @return array[]
     */
    public function provideMethods(): array
    {
        $navigator = $this->getMockBuilder(GraphNavigatorInterface::class)->getMock();

        return [
            [
                'method' => 'visitNull',
                'arguments' => [
                    [],
                    [],
                ],
            ],
            [
                'method' => 'visitString',
                'arguments' => [
                    [],
                    [],
                ],
            ],
            [
                'method' => 'visitBoolean',
                'arguments' => [
                    [],
                    [],
                ],
            ],
            [
                'method' => 'visitDouble',
                'arguments' => [
                    [],
                    [],
                ],
            ],
            [
                'method' => 'visitInteger',
                'arguments' => [
                    [],
                    [],
                ],
            ],
            [
                'method' => 'visitDiscriminatorMapProperty',
                'arguments' => [
                    [],
                    new ClassMetadata('foo'),
                ],
            ],
            [
                'method' => 'visitArray',
                'arguments' => [
                    [],
                    [],
                ],
            ],
            [
                'method' => 'startVisitingObject',
                'arguments' => [
                    new ClassMetadata('foo'),
                    new \stdClass(),
                    [],
                ],
            ],
            [
                'method' => 'visitProperty',
                'arguments' => [
                    new PropertyMetadata('foo', 'bar'),
                    [],
                ],
            ],
            [
                'method' => 'endVisitingObject',
                'arguments' => [
                    new ClassMetadata('foo'),
                    [],
                    [],
                ],
            ],
            [
                'method' => 'getResult',
                'arguments' => [
                    [],
                ],
            ],
            [
                'method' => 'setNavigator',
                'arguments' => [
                    $navigator,
                ],
            ],
        ];
    }
}
