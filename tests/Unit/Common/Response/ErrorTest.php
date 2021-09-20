<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Response;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Response\Error;

/**
 * Error Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ErrorTest extends TestCase
{
    private static $class = Error::class;

    private $values = [
        'metadata' => ['foo'],
        'message' => 'foo',
        'code' => 1,
    ];

    private static $nonNullable = [
        'message' => 'foo',
        'code' => 4,
    ];

    private static $accessors = [
        'metadata' => [
            'reader' => 'getMetadata',
            'writer' => 'setMetadata',
            'remover' => null,
        ],
        'message' => [
            'reader' => 'getMessage',
            'writer' => 'setMessage',
            'remover' => null,
        ],
        'code' => [
            'reader' => 'getCode',
            'writer' => 'setCode',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testErrorReadWritePropertiesMethods(): void
    {
        $instance = new self::$class();

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
