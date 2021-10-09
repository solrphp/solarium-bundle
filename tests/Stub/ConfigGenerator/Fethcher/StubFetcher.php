<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Fethcher;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\FetcherInterface;

/**
 * StubFetcher.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StubFetcher implements FetcherInterface
{
    /**
     * {@inheritDoc}
     */
    public function fetchXml(string $core): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><foo></foo>';
    }
}
