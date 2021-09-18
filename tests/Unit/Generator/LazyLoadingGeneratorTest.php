<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Generator\LazyLoadingGenerator;

/**
 * LazyLoadingGeneratorTest.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class LazyLoadingGeneratorTest extends TestCase
{
    /**
     * test generate.
     */
    public function testGenerate(): void
    {
        $lazyLoader = new LazyLoadingGenerator($this->getGenerator());
        $results = iterator_to_array($lazyLoader);

        self::assertSame(range(0, 2), $results);
    }

    /**
     * test empty generator.
     */
    public function testEmptyGenerator(): void
    {
        $lazyLoader = new LazyLoadingGenerator($this->emptyGenerator());
        $results = iterator_to_array($lazyLoader);

        self::assertSame([], $results);
    }

    /**
     * test repeat.
     */
    public function testRepeat(): void
    {
        $lazyLoader = new LazyLoadingGenerator($this->getGenerator());

        iterator_to_array($lazyLoader);

        $results = iterator_to_array($lazyLoader);

        self::assertSame(range(0, 2), $results);
    }

    /**
     * test partial.
     */
    public function testPartial(): void
    {
        $lazyLoader = new LazyLoadingGenerator($this->getGenerator());

        foreach ($lazyLoader as $value) {
            if (1 === $value) {
                break;
            }
        }

        $results = iterator_to_array($lazyLoader);

        self::assertSame(range(0, 2), $results);
    }

    /**
     * test get generator.
     */
    public function testGetGenerator(): void
    {
        $generator = $this->getGenerator();
        $lazyLoader = new LazyLoadingGenerator($generator);

        self::assertSame($generator, $lazyLoader->getInnerIterator());
    }

    /**
     * test get cache.
     */
    public function testGetCache(): void
    {
        $lazyLoader = new LazyLoadingGenerator($this->getGenerator());
        $results = iterator_to_array($lazyLoader);

        self::assertSame($results, $lazyLoader->getCache());
    }

    /**
     * @return \Generator
     */
    private function getGenerator(): \Generator
    {
        foreach (range(0, 2) as $value) {
            yield $value;
        }
    }

    /**
     * @return \Generator
     */
    private function emptyGenerator(): \Generator
    {
        if (false) {
            yield 0;
        }
    }
}
