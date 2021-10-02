<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestHandler;

use Solrphp\SolariumBundle\ConfigGenerator\Util\QueryUtil;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * ComponentsVisitor.
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
    ];

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$requestHandler): void
    {
        $components = $crawler->filterXPath(QueryUtil::attributeValues(self::$root, self::$nodeAttribute, self::$attributes));

        if (0 === $components->count()) {
            return;
        }

        $node = [];

        $components->children()->each(static function (Crawler $crawler) use (&$node) {
            $node[] = $crawler->text();
        });

        $requestHandler['components'] = $closure($node);
    }
}
