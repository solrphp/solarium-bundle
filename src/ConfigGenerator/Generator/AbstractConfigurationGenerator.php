<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Generator;

use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\FetcherInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Abstract Configuration Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
abstract class AbstractConfigurationGenerator
{
    /**
     * @var string[]
     */
    protected static array $nodeTypes = [];

    /**
     * @var iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\GeneratorHandlerInterface>
     */
    private iterable $handlerChain;

    /**
     * @var \Solrphp\SolariumBundle\ConfigGenerator\Contract\FetcherInterface
     */
    private FetcherInterface $fetcher;

    /**
     * @var \Closure
     */
    private \Closure $closure;

    /**
     * @var array<string, mixed>
     */
    private array $nodes = [];

    /**
     * @param iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\GeneratorHandlerInterface> $handlerChain
     * @param \Solrphp\SolariumBundle\ConfigGenerator\Contract\FetcherInterface                    $fetcher
     */
    public function __construct(iterable $handlerChain, FetcherInterface $fetcher)
    {
        $this->handlerChain = $handlerChain;
        $this->fetcher = $fetcher;
        $this->closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
    }

    /**
     * @param string   $core
     * @param string[] $types
     */
    public function generate(string $core, array $types): void
    {
        $this->setTypes(array_intersect(static::$nodeTypes, $types));
        $crawler = new Crawler($this->fetcher->fetchXml($core));

        foreach ($this->getTypes() as $type) {
            foreach ($this->handlerChain as $handler) {
                if (false === $handler->supports($type)) {
                    continue;
                }

                $this->nodes[$type] = $handler->handle($crawler, $this->closure);
            }
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getNodes(): ?array
    {
        return $this->nodes ?: null;
    }

    /**
     * @param string[] $types
     */
    abstract public function setTypes(array $types): void;

    /**
     * @return string[]
     */
    abstract public function getTypes(): array;
}
