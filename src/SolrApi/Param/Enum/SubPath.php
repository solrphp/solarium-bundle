<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Enum;

/**
 * Params SubPath.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SubPath
{
    /**
     * @see https://solr.apache.org/guide/request-parameters-api.html#the-request-parameters-endpoint
     */
    public const LIST_PARAMS = '';

    public const ALL = [
        self::LIST_PARAMS,
    ];

    public const RESPONSE_CLASSES = [];

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
