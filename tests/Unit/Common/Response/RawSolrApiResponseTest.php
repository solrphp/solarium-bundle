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
use Solrphp\SolariumBundle\Common\Response\Header;
use Solrphp\SolariumBundle\Common\Response\RawSolrApiResponse;

/**
 * Raw SolrAp iResponse Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class RawSolrApiResponseTest extends TestCase
{
    /**
     * @var class-string
     */
    private static string $class = RawSolrApiResponse::class;

    /**
     * @var array<string, string>
     */
    private array $values = [
        'headerResponse' => Header::class,
        'error' => Error::class,
        'body' => 'foo',
    ];

    /**
     * @var array<string, string>
     */
    private static array $nonNullable = [
        'headerResponse' => Header::class,
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private static array $accessors = [
        'headerResponse' => [
            'reader' => 'getResponseHeader',
            'writer' => 'setResponseHeader',
            'remover' => null,
        ],
        'error' => [
            'reader' => 'getError',
            'writer' => 'setError',
            'remover' => null,
        ],
        'body' => [
            'reader' => 'getBody',
            'writer' => 'setBody',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testHeaderReadWritePropertiesMethods(): void
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
