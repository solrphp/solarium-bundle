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

/**
 * Fetcher Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface FetcherInterface
{
    /**
     * @param string $core
     *
     * @return string
     */
    public function fetchXml(string $core): string;
}
