<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\ParamGeneratorVisitorInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Util\QueryUtil;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Parameters Visitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParametersVisitor implements ParamGeneratorVisitorInterface
{
    /**
     * @var string
     */
    private static string $root = 'lst';

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$result): void
    {
        $invariants = $crawler->filterXPath(QueryUtil::nodeNameExclude(self::$root, ['lst']));

        if (0 === $invariants->count()) {
            return;
        }

        $nodes = [];

        $invariants->each(static function (Crawler $crawler) use (&$nodes) {
            $nodes[] = ['name' => $crawler->attr('name'), 'value' => $crawler->text()];
        });

        $result['parameters'] = $closure($nodes);
    }
}
