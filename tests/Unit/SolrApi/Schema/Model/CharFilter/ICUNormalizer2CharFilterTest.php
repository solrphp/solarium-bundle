<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Model\CharFilter;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter\ICUNormalizer2CharFilter;
use Solrphp\SolariumBundle\Tests\Helper\Value;

/**
 * ICUNormalizer2Char Filter Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ICUNormalizer2CharFilterTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = ICUNormalizer2CharFilter::class;

    /**
     * @var array|string[]
     */
    private array $values = [
        'class' => 'foo',
        'name' => 'foo',
        'mode' => 'foo',
        'filter' => 'foo',
    ];

    /**
     * @var array|string[]
     */
    private static array $nonNullable = [
        'class' => 'foo',
    ];

    /**
     * @var array|array[]
     */
    private static array $accessors = [
        'class' => [
            'reader' => 'getClass',
            'writer' => 'setClass',
            'remover' => null,
        ],
        'name' => [
            'reader' => 'getName',
            'writer' => 'setName',
            'remover' => null,
        ],
        'mode' => [
            'reader' => 'getMode',
            'writer' => 'setMode',
            'remover' => null,
        ],
        'filter' => [
            'reader' => 'getFilter',
            'writer' => 'setFilter',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testICUNormalizer2CharFilterReadWritePropertiesMethods(): void
    {
        $instance = new self::$class();

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

        self::assertSame(array_keys(Value::keysort($this->values)), array_keys(Value::keysort($instance->jsonSerialize())));

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

            self::assertSame(array_keys(Value::keysort(self::$nonNullable)), array_keys(Value::keysort($instance->jsonSerialize())));
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
