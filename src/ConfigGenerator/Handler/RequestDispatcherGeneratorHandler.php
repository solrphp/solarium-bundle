<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestDispatcher\RequestParserVisitor;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * RequestDispatcherGeneratorHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestDispatcherGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var string
     */
    private static string $root = '//config/requestDispatcher';

    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'handleSelect',
    ];

    /**
     * @var iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>
     */
    private iterable $visitors;

    /**
     * @param iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>|null $visitors
     */
    public function __construct(iterable $visitors = null)
    {
        $this->visitors = $visitors ?? $this->getDefaultVisitors();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        if (false === $combined = @array_combine(self::$attributes, $crawler->filterXPath(self::$root)->extract(self::$attributes))) {
            return [];
        }

        $node = $closure(ArrayUtil::filter($combined));

        foreach ($this->visitors as $visitor) {
            $visitor->visit($crawler, $closure, $node);
        }

        return $node;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        return ConfigGenerator::TYPE_REQUEST_DISPATCHER === $type;
    }

    /**
     * @return \Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestDispatcher\RequestParserVisitor[]
     */
    private function getDefaultVisitors(): array
    {
        return [
            new RequestParserVisitor(),
        ];
    }
}
