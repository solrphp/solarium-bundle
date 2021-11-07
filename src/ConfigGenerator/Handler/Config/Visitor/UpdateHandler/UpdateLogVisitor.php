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
 * UpdateLogVisitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UpdateLogVisitor implements ConfigGeneratorVisitorInterface
{
    /**
     * @var string
     */
    private static string $root = '//config/updateHandler/updateLog';

    /**
     * @var string
     */
    private static string $nodeAttribute = 'name';

    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'dir',
        'numRecordsToKeep',
        'maxNumLogsToKeep',
        'numVersionBuckets',
    ];

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$result): void
    {
        $node = [];

        foreach ($crawler->filterXPath(QueryUtil::attributeValues(self::$root, self::$nodeAttribute, self::$attributes)) as $updateLogProperty) {
            /* @var \DOMElement $updateLogProperty */
            $node[$updateLogProperty->getAttribute(self::$nodeAttribute)] = $updateLogProperty->textContent; /* @phpstan-ignore-line */
        }

        $result['update_log'] = $closure($node);
    }
}
