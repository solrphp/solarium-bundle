<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestHandler;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Util\QueryUtil;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Components Visitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ComponentsVisitor implements ConfigGeneratorVisitorInterface
{
    /**
     * @var string
     */
    private static string $root = 'requestHandler';

    /**
     * @var string
     */
    private static string $nodeAttribute = 'name';

    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'components',
        '_components_',
    ];

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$result): void
    {
        $components = $crawler->filterXPath(QueryUtil::attributeValues(self::$root, self::$nodeAttribute, self::$attributes));

        if (0 === $components->count()) {
            return;
        }

        $node = [];

        $components->children()->each(static function (Crawler $crawler) use (&$node) {
            $node[] = $crawler->text();
        });

        $result['components'] = $closure($node);
    }
}
