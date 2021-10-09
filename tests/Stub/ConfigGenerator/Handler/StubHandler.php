<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Handler;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\GeneratorHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * StubHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StubHandler implements GeneratorHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $type): bool
    {
        return true;
    }
}
