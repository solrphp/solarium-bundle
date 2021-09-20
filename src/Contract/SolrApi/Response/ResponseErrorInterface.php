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
 * Response Error Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ResponseErrorInterface
{
    /**
     * get error code as provided by solr.
     *
     * @return int
     */
    public function getCode(): int;

    /**
     * get error message.
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return string[]
     */
    public function getMetaData(): iterable;
}
