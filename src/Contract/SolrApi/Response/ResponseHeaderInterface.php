<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\SolrApi\Response;

/**
 * Response Header Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ResponseHeaderInterface
{
    /**
     * get status code (Solr status 0 means ok).
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * get query time as provided by solr.
     *
     * @return int
     */
    public function getQTime(): int;
}
