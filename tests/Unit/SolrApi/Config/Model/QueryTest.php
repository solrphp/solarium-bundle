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
use Solrphp\SolariumBundle\SolrApi\Config\Model\Cache;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\Tests\Helper\ArrayHelper;

/**
 * Query Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class QueryTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = Query::class;

    /**
     * @var array<string, mixed>
     */
    private array $values = [
        'useFilterForSortedQuery' => false,
        'queryResultWindowSize' => 3,
        'queryResultMaxDocsCached' => 4,
        'enableLazyFieldLoading' => false,
        'maxBooleanClauses' => 3,
        'filterCache' => Cache::class,
        'queryResultCache' => Cache::class,
        'documentCache' => Cache::class,
        'useCircuitBreakers' => false,
        'memoryCircuitBreakerThresholdPct' => 5,
    ];

    /**
     * @var array|string[]
     */
    private static array $nonNullable = [
        'filterCache' => Cache::class,
        'queryResultCache' => Cache::class,
        'documentCache' => Cache::class,
    ];

    /**
     * @var array|array[]
     */
    private static array $accessors = [
        'useFilterForSortedQuery' => [
            'reader' => 'getUseFilterForSortedQuery',
            'writer' => 'setUseFilterForSortedQuery',
            'remover' => null,
        ],
        'queryResultWindowSize' => [
            'reader' => 'getQueryResultWindowSize',
            'writer' => 'setQueryResultWindowSize',
            'remover' => null,
        ],
        'queryResultMaxDocsCached' => [
            'reader' => 'getQueryResultMaxDocsCached',
            'writer' => 'setQueryResultMaxDocsCached',
            'remover' => null,
        ],
        'enableLazyFieldLoading' => [
            'reader' => 'getEnableLazyFieldLoading',
            'writer' => 'setEnableLazyFieldLoading',
            'remover' => null,
        ],
        'maxBooleanClauses' => [
            'reader' => 'getMaxBooleanClauses',
            'writer' => 'setMaxBooleanClauses',
            'remover' => null,
        ],
        'filterCache' => [
            'reader' => 'getFilterCache',
            'writer' => 'setFilterCache',
            'remover' => null,
        ],
        'queryResultCache' => [
            'reader' => 'getQueryResultCache',
            'writer' => 'setQueryResultCache',
            'remover' => null,
        ],
        'documentCache' => [
            'reader' => 'getDocumentCache',
            'writer' => 'setDocumentCache',
            'remover' => null,
        ],
        'useCircuitBreakers' => [
            'reader' => 'getUseCircuitBreakers',
            'writer' => 'setUseCircuitBreakers',
            'remover' => null,
        ],
        'memoryCircuitBreakerThresholdPct' => [
            'reader' => 'getMemoryCircuitBreakerThresholdPct',
            'writer' => 'setMemoryCircuitBreakerThresholdPct',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testQueryReadWritePropertiesMethods(): void
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
