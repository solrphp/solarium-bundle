<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Enum\SubPath;

use Solrphp\SolariumBundle\SolrApi\Response\ConfigResponse;

/**
 * Config sub paths.
 *
 * @see https://lucene.apache.org/solr/guide/8_4/config-api.html#config-api-endpoints
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class Config
{
    public const GET_CONFIG = '';
    public const GET_OVERLAY = 'overlay';
    public const GET_SEARCH_COMPONENTS = 'searchComponent';
    public const GET_REQUEST_HANDLERS = 'requestHandler';
    public const GET_QUERY = 'query';

    /**
     * string[].
     */
    public const SUB_PATHS = [
        self::GET_CONFIG,
        self::GET_OVERLAY,
        self::GET_SEARCH_COMPONENTS,
        self::GET_REQUEST_HANDLERS,
        self::GET_QUERY,
    ];

    public const RESPONSE_CLASSES = [
        self::GET_SEARCH_COMPONENTS => ConfigResponse::class,
        self::GET_REQUEST_HANDLERS => ConfigResponse::class,
        self::GET_QUERY => ConfigResponse::class,
    ];

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
