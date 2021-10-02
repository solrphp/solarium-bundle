<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\Query;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * FilterCacheVisitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FilterCacheVisitor implements ConfigGeneratorVisitorInterface
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
    private static string $root = '//config/query/filterCache';

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$query): void
    {
        $node = [];

        foreach ($crawler->filterXPath(self::$root)->extract(self::$attributes) as $filterCache) {
            if (false === $combined = @array_combine(self::$attributes, $filterCache)) {
                continue;
            }

            $node[] = $closure(ArrayUtil::filter($combined));
        }

        if (null !== $cache = array_shift($node)) {
            $query['filter_cache'] = $cache;
        }
    }
}
