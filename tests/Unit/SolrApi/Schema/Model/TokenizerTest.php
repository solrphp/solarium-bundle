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
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Tokenizer;
use Solrphp\SolariumBundle\Tests\Helper\ArrayHelper;

/**
 * Tokenizer Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class TokenizerTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = Tokenizer::class;

    /**
     * @var array<string, string|int>
     */
    private array $values = [
        'class' => 'foo',
        'pattern' => 'foo',
        'minGramSize' => 3,
        'maxGramSize' => 3,
        'delimiter' => 'foo',
        'replace' => 'foo',
        'group' => 3,
        'rule' => 'foo',
        'mode' => 'foo',
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
        'pattern' => [
            'reader' => 'getPattern',
            'writer' => 'setPattern',
            'remover' => null,
        ],
        'minGramSize' => [
            'reader' => 'getMinGramSize',
            'writer' => 'setMinGramSize',
            'remover' => null,
        ],
        'maxGramSize' => [
            'reader' => 'getMaxGramSize',
            'writer' => 'setMaxGramSize',
            'remover' => null,
        ],
        'delimiter' => [
            'reader' => 'getDelimiter',
            'writer' => 'setDelimiter',
            'remover' => null,
        ],
        'replace' => [
            'reader' => 'getReplace',
            'writer' => 'setReplace',
            'remover' => null,
        ],
        'group' => [
            'reader' => 'getGroup',
            'writer' => 'setGroup',
            'remover' => null,
        ],
        'rule' => [
            'reader' => 'getRule',
            'writer' => 'setRule',
            'remover' => null,
        ],
        'mode' => [
            'reader' => 'getMode',
            'writer' => 'setMode',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testTokenizerReadWritePropertiesMethods(): void
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

        self::assertSame(array_keys(ArrayHelper::keysort($this->values)), array_keys(ArrayHelper::keysort($instance->jsonSerialize())));

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

            self::assertSame(array_keys(ArrayHelper::keysort(self::$nonNullable)), array_keys(ArrayHelper::keysort($instance->jsonSerialize())));
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
