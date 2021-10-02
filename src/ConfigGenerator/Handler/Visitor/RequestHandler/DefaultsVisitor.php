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
 * DefaultsVisitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DefaultsVisitor implements ConfigGeneratorVisitorInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'defaults',
    ];

    /**
     * @var string
     */
    private static string $root = 'requestHandler';

    /**
     * @var string
     */
    private static string $nodeAttribute = 'name';

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$requestHandler): void
    {
        $defaults = $crawler->filterXPath(QueryUtil::attributeValues(self::$root, self::$nodeAttribute, self::$attributes));

        if (0 === $defaults->count()) {
            return;
        }

        $node = [];

        $defaults->children()->each(static function (Crawler $crawler) use (&$node) {
            $node[$crawler->attr('name')] = $crawler->text();
        });

        $requestHandler['defaults'] = $closure($node);
    }
}
