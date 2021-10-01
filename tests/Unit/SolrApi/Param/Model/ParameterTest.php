<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Model;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter;

/**
 * Parameter Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ParameterTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = Parameter::class;

    /**
     * @var array<string, string>
     */
    private array $values = [
        'name' => 'foo',
        'value' => 'qux',
    ];

    /**
     * @var array<string, string>
     */
    private static array $nonNullable = [
        'name' => 'foo',
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private static array $accessors = [
        'name' => [
            'reader' => 'getName',
            'writer' => 'setName',
            'remover' => null,
        ],
        'value' => [
            'reader' => 'getValue',
            'writer' => 'setValue',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testParameterReadWritePropertiesMethods(): void
    {
        $instance = new self::$class('foo', 'bar');

        self::assertInstanceOf(\JsonSerializable::class, $instance);

        foreach ($this->values as $name => $value) {
            $reader = self::$accessors[$name]['reader'];
            $writer = self::$accessors[$name]['writer'];
            $remover = self::$accessors[$name]['remover'];

            $this->values[$name] = $value = $this->value($value);
            $instance->$writer($value);

            if (null === $remover) {
                self::assertSame($value, $instance->$reader());
            } else {
                self::assertContains($value, $instance->$reader());
            }
        }

        self::assertSame(['foo' => 'qux'], $instance->jsonSerialize());

        foreach (self::$accessors as $property => $accessors) {
            if (null === $remover = $accessors['remover']) {
                continue;
            }

            $reader = $accessors['reader'];

            self::assertTrue($instance->$remover($this->values[$property]));
            self::assertNotContains($this->values[$property], $instance->$reader());
            self::assertFalse($instance->$remover($this->values[$property]));
        }

        $instance = new self::$class('foo', 'bar');

        if (0 !== \count(self::$nonNullable)) {
            foreach (self::$nonNullable as $name => $value) {
                $writer = self::$accessors[$name]['writer'];
                $value = $this->value($value);
                $instance->$writer($value);
            }

            self::assertSame(['foo' => 'bar'], $instance->jsonSerialize());
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
        return \is_string($value) && class_exists($value) ? new $value() : $value;
    }
}
