<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\UpdateHandler;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Util\QueryUtil;
use Symfony\Component\DomCrawler\Crawler;

/**
 * CommitWithinVisitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CommitWithinVisitor implements ConfigGeneratorVisitorInterface
{
    /**
     * @var string
     */
    private static string $root = '//config/updateHandler/commitWithin';

    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'softCommit',
    ];

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$result): void
    {
        $node = [];

        foreach ($crawler->filterXPath(QueryUtil::nodeNames(self::$root, self::$attributes)) as $autoCommit) {
            $node[$autoCommit->nodeName] = $autoCommit->textContent;
        }

        $result['commit_within'] = $closure($node);
    }
}
