<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Model\Filter;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Tests\Helper\Value;

/**
 * Word Delimiter Graph Filter Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class WordDelimiterGraphFilterTest extends TestCase
{
    private static $class = 'Solrphp\\SolariumBundle\\SolrApi\\Schema\\Model\\Filter\\WordDelimiterGraphFilter';

    private $values = [
        'class' => 'foo',
        'generateWordParts' => 5,
        'generateNumberParts' => 2,
        'splitOnCaseChange' => 5,
        'splitOnNumerics' => 3,
        'catenateWords' => 5,
        'catenateNumbers' => 3,
        'catenateAll' => 4,
        'preserveOriginal' => 1,
        'protected' => 'foo',
        'stemEnglishPossessive' => 3,
        'types' => 'foo',
    ];

    private static $nonNullable = [
        'class' => 'foo',
    ];

    private static $accessors = [
        'class' => [
            'reader' => 'getClass',
            'writer' => 'setClass',
            'remover' => null,
        ],
        'generateWordParts' => [
            'reader' => 'getGenerateWordParts',
            'writer' => 'setGenerateWordParts',
            'remover' => null,
        ],
        'generateNumberParts' => [
            'reader' => 'getGenerateNumberParts',
            'writer' => 'setGenerateNumberParts',
            'remover' => null,
        ],
        'splitOnCaseChange' => [
            'reader' => 'getSplitOnCaseChange',
            'writer' => 'setSplitOnCaseChange',
            'remover' => null,
        ],
        'splitOnNumerics' => [
            'reader' => 'getSplitOnNumerics',
            'writer' => 'setSplitOnNumerics',
            'remover' => null,
        ],
        'catenateWords' => [
            'reader' => 'getCatenateWords',
            'writer' => 'setCatenateWords',
            'remover' => null,
        ],
        'catenateNumbers' => [
            'reader' => 'getCatenateNumbers',
            'writer' => 'setCatenateNumbers',
            'remover' => null,
        ],
        'catenateAll' => [
            'reader' => 'getCatenateAll',
            'writer' => 'setCatenateAll',
            'remover' => null,
        ],
        'preserveOriginal' => [
            'reader' => 'getPreserveOriginal',
            'writer' => 'setPreserveOriginal',
            'remover' => null,
        ],
        'protected' => [
            'reader' => 'getProtected',
            'writer' => 'setProtected',
            'remover' => null,
        ],
        'stemEnglishPossessive' => [
            'reader' => 'getStemEnglishPossessive',
            'writer' => 'setStemEnglishPossessive',
            'remover' => null,
        ],
        'types' => [
            'reader' => 'getTypes',
            'writer' => 'setTypes',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testWordDelimiterGraphFilterReadWritePropertiesMethods(): void
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
