<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Model;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\Tests\Helper\Value;

/**
 * Field Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class FieldTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = Field::class;

    /**
     * @var array
     */
    private array $values = [
        'indexed' => false,
        'stored' => false,
        'docValues' => false,
        'sortMissingFirst' => false,
        'sortMissingLast' => false,
        'multiValued' => false,
        'uninvertible' => false,
        'omitNorms' => false,
        'omitTermFreqAndPositions' => false,
        'omitPositions' => false,
        'termVectors' => false,
        'termPositions' => false,
        'termOffsets' => false,
        'termPayloads' => false,
        'required' => false,
        'useDocValuesAsStored' => false,
        'large' => false,
        'name' => 'foo',
        'type' => 'foo',
        'default' => 'foo',
    ];

    /**
     * @var array|string[]
     */
    private static array $nonNullable = [
        'name' => 'foo',
    ];

    /**
     * @var array|array[]
     */
    private static array $accessors = [
        'indexed' => [
            'reader' => 'getIndexed',
            'writer' => 'setIndexed',
            'remover' => null,
        ],
        'stored' => [
            'reader' => 'getStored',
            'writer' => 'setStored',
            'remover' => null,
        ],
        'docValues' => [
            'reader' => 'getDocValues',
            'writer' => 'setDocValues',
            'remover' => null,
        ],
        'sortMissingFirst' => [
            'reader' => 'getSortMissingFirst',
            'writer' => 'setSortMissingFirst',
            'remover' => null,
        ],
        'sortMissingLast' => [
            'reader' => 'getSortMissingLast',
            'writer' => 'setSortMissingLast',
            'remover' => null,
        ],
        'multiValued' => [
            'reader' => 'getMultiValued',
            'writer' => 'setMultiValued',
            'remover' => null,
        ],
        'uninvertible' => [
            'reader' => 'getUninvertible',
            'writer' => 'setUninvertible',
            'remover' => null,
        ],
        'omitNorms' => [
            'reader' => 'getOmitNorms',
            'writer' => 'setOmitNorms',
            'remover' => null,
        ],
        'omitTermFreqAndPositions' => [
            'reader' => 'getOmitTermFreqAndPositions',
            'writer' => 'setOmitTermFreqAndPositions',
            'remover' => null,
        ],
        'omitPositions' => [
            'reader' => 'getOmitPositions',
            'writer' => 'setOmitPositions',
            'remover' => null,
        ],
        'termVectors' => [
            'reader' => 'getTermVectors',
            'writer' => 'setTermVectors',
            'remover' => null,
        ],
        'termPositions' => [
            'reader' => 'getTermPositions',
            'writer' => 'setTermPositions',
            'remover' => null,
        ],
        'termOffsets' => [
            'reader' => 'getTermOffsets',
            'writer' => 'setTermOffsets',
            'remover' => null,
        ],
        'termPayloads' => [
            'reader' => 'getTermPayloads',
            'writer' => 'setTermPayloads',
            'remover' => null,
        ],
        'required' => [
            'reader' => 'getRequired',
            'writer' => 'setRequired',
            'remover' => null,
        ],
        'useDocValuesAsStored' => [
            'reader' => 'getUseDocValuesAsStored',
            'writer' => 'setUseDocValuesAsStored',
            'remover' => null,
        ],
        'large' => [
            'reader' => 'getLarge',
            'writer' => 'setLarge',
            'remover' => null,
        ],
        'name' => [
            'reader' => 'getName',
            'writer' => 'setName',
            'remover' => null,
        ],
        'type' => [
            'reader' => 'getType',
            'writer' => 'setType',
            'remover' => null,
        ],
        'default' => [
            'reader' => 'getDefault',
            'writer' => 'setDefault',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFieldReadWritePropertiesMethods(): void
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
