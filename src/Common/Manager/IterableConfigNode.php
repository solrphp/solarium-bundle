<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeInterface;

/**
 * Config Node.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class IterableConfigNode implements ConfigNodeInterface
{
    /**
     * @var class-string
     */
    private string $type;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var ArrayCollection<int, mixed>
     */
    private ArrayCollection $elements;

    /**
     * @param class-string                      $type
     * @param string                            $path
     * @param ArrayCollection<array-key, mixed> $elements
     */
    public function __construct(string $type, string $path, ArrayCollection $elements)
    {
        $this->type = $type;
        $this->path = $path;
        $this->elements = $elements;
    }

    /**
     * @return class-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return \Generator<array-key, mixed>
     */
    public function get(): \Generator
    {
        foreach ($this->elements as $element) {
            yield $element;
        }
    }
}
