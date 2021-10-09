<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Contract;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Generator Visitor Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface GeneratorVisitorInterface
{
    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Closure                              $closure
     * @param array<string, string>                 $query
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$query): void;
}
