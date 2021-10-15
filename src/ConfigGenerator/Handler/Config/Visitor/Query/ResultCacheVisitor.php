<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * QueryResultCacheVisitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ResultCacheVisitor implements ConfigGeneratorVisitorInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'autowarmCount',
        'size',
        'initialSize',
        'class',
        'name',
    ];

    /**
     * @var string
     */
    private static string $root = '//config/query/queryResultCache';

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$result): void
    {
        $node = [];

        foreach ($crawler->filterXPath(self::$root)->extract(self::$attributes) as $queryResultCache) {
            if (false === $combined = @array_combine(self::$attributes, $queryResultCache)) {
                continue;
            }

            $node[] = $closure(ArrayUtil::filter($combined));
        }

        if (null !== $cache = array_shift($node)) {
            $result['query_result_cache'] = $cache;
        }
    }
}
