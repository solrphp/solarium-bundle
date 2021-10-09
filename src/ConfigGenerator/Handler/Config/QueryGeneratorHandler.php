<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Config;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorHandlerInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ConfigConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\DocumentCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\FieldValueCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\FilterCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\ResultCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Util\QueryUtil;
use Symfony\Component\DomCrawler\Crawler;

/**
 * QueryGeneratorHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class QueryGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var string
     */
    private static string $root = '//config/query';

    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'useFilterForSortedQuery',
        'queryResultWindowSize',
        'queryResultMaxDocsCached',
        'enableLazyFieldLoading',
        'maxBooleanClauses',
    ];

    /**
     * @var iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface>
     */
    private iterable $visitors;

    /**
     * @param iterable|\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface[]|null $visitors
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
        $node = [];

        foreach ($crawler->filterXPath(QueryUtil::nodeNames(self::$root, self::$attributes)) as $query) {
            $node[$query->nodeName] = $query->textContent;
        }

        $node = $closure($node);

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
        return ConfigConfigurationGenerator::TYPE_QUERY === $type;
    }

    /**
     * @return array<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface>
     */
    private function getDefaultVisitors(): array
    {
        return [
            new FilterCacheVisitor(),
            new ResultCacheVisitor(),
            new DocumentCacheVisitor(),
            new FieldValueCacheVisitor(),
        ];
    }
}
