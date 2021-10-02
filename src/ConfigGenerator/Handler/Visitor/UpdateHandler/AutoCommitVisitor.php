<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler;

use Solrphp\SolariumBundle\ConfigGenerator\Util\QueryUtil;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * AutoCommitVisitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AutoCommitVisitor implements ConfigGeneratorVisitorInterface
{
    /**
     * @var string
     */
    private static string $root = '//config/updateHandler/autoCommit';

    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'maxDocs',
        'maxTime',
        'maxSize',
        'openSearcher',
    ];

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$updateHandler): void
    {
        $node = [];

        foreach ($crawler->filterXPath(QueryUtil::nodeNames(self::$root, self::$attributes)) as $autoCommit) {
            $node[$autoCommit->nodeName] = $autoCommit->textContent;
        }

        $updateHandler['auto_commit'] = $closure($node);
    }
}
