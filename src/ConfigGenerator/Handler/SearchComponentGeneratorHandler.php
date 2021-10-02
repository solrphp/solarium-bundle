<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * SearchComponentGeneratorHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SearchComponentGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'name',
        'class',
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        $nodes = [];

        foreach ($crawler->filterXPath('//config/searchComponent')->extract(self::$attributes) as $field) {
            if (false === $combined = @array_combine(self::$attributes, $field)) {
                continue;
            }

            $nodes[] = $closure(ArrayUtil::filter($combined));
        }

        return $nodes;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        return ConfigGenerator::TYPE_SEARCH_COMPONENT === $type;
    }
}
