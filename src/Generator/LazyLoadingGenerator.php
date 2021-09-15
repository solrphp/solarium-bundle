<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Generator;

/**
 * Cached Generator.
 *
 * @author wicliff <wwolda@gmail.com>
 *
 * @phpstan-template Treturn of \Generator
 *
 * @implements \OuterIterator<int, Treturn>
 */
class LazyLoadingGenerator implements \OuterIterator
{
    /**
     * @var array<int, mixed>
     */
    private array $cache = [];

    /**
     * @var \Generator<int, mixed>
     */
    private \Generator $generator;

    /**
     * @param \Generator<int, mixed> $generator
     */
    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
        $this->cacheCurrent();
    }

    /**
     * @return false|mixed
     */
    public function current()
    {
        return current($this->cache);
    }

    /**
     * next.
     */
    public function next(): void
    {
        if ($this->generator->key() === key($this->cache)) {
            $this->generator->next();
            $this->cacheCurrent();
        }

        next($this->cache);
    }

    /**
     * @return int|null
     */
    public function key(): ?int
    {
        return key($this->cache);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return null !== key($this->cache);
    }

    /**
     * rewind.
     */
    public function rewind(): void
    {
        reset($this->cache);
    }

    /**
     * @return \Generator<int, Treturn>
     */
    public function getInnerIterator(): \Generator
    {
        return $this->generator;
    }

    /**
     * @return array<int, mixed>
     */
    public function getCache(): array
    {
        return $this->cache;
    }

    /**
     * cache.
     */
    private function cacheCurrent(): void
    {
        if ($this->generator->valid()) {
            $this->cache[$this->generator->key()] = $this->generator->current();
        }
    }
}
