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
 * Generator Handler Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface GeneratorHandlerInterface
{
    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Closure                              $closure
     *
     * @return array<int|string, mixed>
     */
    public function handle(Crawler $crawler, \Closure $closure): array;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool;
}
