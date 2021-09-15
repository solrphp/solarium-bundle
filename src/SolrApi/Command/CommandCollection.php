<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Command;

use ArrayIterator;

/**
 * Command Collection.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @implements \ArrayAccess<string, array>
 * @implements \IteratorAggregate<string, array>
 */
final class CommandCollection implements \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    /**
     * @var array<string, array>
     */
    private array $commands;

    /**
     * @param array<string, array> $commands
     */
    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    /**
     * @param string                             $command
     * @param array<int, \JsonSerializable|null> $value
     */
    public function set(string $command, array $value): void
    {
        $this->commands[$command] = $value;
    }

    /**
     * @param string            $command
     * @param \JsonSerializable $value
     */
    public function add(string $command, \JsonSerializable $value): void
    {
        $this->commands[$command][] = $value;
    }

    /**
     * @param mixed $command
     *
     * @return \JsonSerializable[]|\JsonSerializable|null
     */
    public function remove($command)
    {
        if (false === $this->containsCommand($command)) {
            return null;
        }

        $removed = $this->commands[$command];

        unset($this->commands[$command]);

        return $removed;
    }

    /**
     * @param mixed $command
     *
     * @return bool
     */
    public function containsCommand($command): bool
    {
        return isset($this->commands[$command]) || \array_key_exists($command, $this->commands);
    }

    /**
     * Clear.
     */
    public function clear(): void
    {
        $this->commands = [];
    }

    /**
     * @return ArrayIterator<string, array>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->commands);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return $this->containsCommand($offset);
    }

    /**
     * @return array<string>|null
     *
     * @param mixed $offset
     */
    public function offsetGet($offset): ?array
    {
        return $this->get($offset);
    }

    /**
     * @param string            $offset
     * @param array<int, mixed> $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter($this->commands);
    }

    /**
     * @param string $command
     *
     * @return array<string>|null
     */
    public function get(string $command): ?array
    {
        return $this->commands[$command] ?? null;
    }
}
