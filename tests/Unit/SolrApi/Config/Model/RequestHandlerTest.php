<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Model;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\Tests\Helper\Value;

/**
 * Request Handler Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class RequestHandlerTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = RequestHandler::class;

    /**
     * @var array|string[]
     */
    private array $values = [
        'name' => 'foo',
        'class' => 'foo',
        'defaults' => Property::class,
        'appends' => Property::class,
        'invariants' => Property::class,
        'components' => 'foo',
        'firstComponents' => 'foo',
        'lastComponents' => 'foo',
    ];

    /**
     * @var array|string[]
     */
    private static array $nonNullable = [
        'name' => 'foo',
        'class' => 'foo',
    ];

    /**
     * @var array<string, array>
     */
    private static array $accessors = [
        'name' => [
            'reader' => 'getName',
            'writer' => 'setName',
            'remover' => null,
        ],
        'class' => [
            'reader' => 'getClass',
            'writer' => 'setClass',
            'remover' => null,
        ],
        'defaults' => [
            'reader' => 'getDefaults',
            'writer' => 'addDefault',
            'remover' => 'removeDefault',
        ],
        'appends' => [
            'reader' => 'getAppends',
            'writer' => 'addAppend',
            'remover' => 'removeAppend',
        ],
        'invariants' => [
            'reader' => 'getInvariants',
            'writer' => 'addInvariant',
            'remover' => 'removeInvariant',
        ],
        'components' => [
            'reader' => 'getComponents',
            'writer' => 'addComponent',
            'remover' => 'removeComponent',
        ],
        'firstComponents' => [
            'reader' => 'getFirstComponents',
            'writer' => 'addFirstComponent',
            'remover' => 'removeFirstComponent',
        ],
        'lastComponents' => [
            'reader' => 'getLastComponents',
            'writer' => 'addLastComponent',
            'remover' => 'removeLastComponent',
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRequestHandlerReadWritePropertiesMethods(): void
    {
        $instance = new self::$class();

        self::assertInstanceOf(\JsonSerializable::class, $instance);

        foreach ($this->values as $name => $value) {
            $reader = self::$accessors[$name]['reader'];
            $writer = self::$accessors[$name]['writer'];
            $remover = self::$accessors[$name]['remover'];

            try {
                $value = $this->value($value);
            } catch (\Exception $e) {
                $value = new Property('foo', 'qux');
            }

            $this->values[$name] = $value;
            $instance->$writer($value);

            if (null === $remover) {
                self::assertSame($value, $instance->$reader());
            } else {
                self::assertContains($value, $instance->$reader());
            }
        }

        self::assertSame(Value::ksortnormalized($this->values), array_keys(Value::keysort($instance->jsonSerialize())));

        foreach (self::$accessors as $property => $accessors) {
            if (null === $remover = $accessors['remover']) {
                continue;
            }

            $reader = $accessors['reader'];

            self::assertTrue($instance->$remover($this->values[$property]));
            self::assertNotContains($this->values[$property], $instance->$reader());
            self::assertFalse($instance->$remover($this->values[$property]));
        }

        $instance = new self::$class();

        if (0 !== \count(self::$nonNullable)) {
            foreach (self::$nonNullable as $name => $value) {
                $writer = self::$accessors[$name]['writer'];
                $value = $this->value($value);
                $instance->$writer($value);
            }

            self::assertSame(Value::ksortnormalized(self::$nonNullable), array_keys(Value::keysort($instance->jsonSerialize())));
        } else {
            self::assertEmpty($instance->jsonSerialize());
        }
    }

    /**
     * @param string|int|float|bool|iterable<int|string, mixed> $value
     *
     * @return object|string|int|float|bool|iterable<int|string, mixed>
     */
    private function value($value)
    {
        return \is_string($value) && class_exists($value) ? new $value('foo', 'bar') : $value;
    }
}
