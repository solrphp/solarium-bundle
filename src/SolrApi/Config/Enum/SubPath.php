<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Enum;

use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;

/**
 * Config sub paths.
 *
 * @see https://lucene.apache.org/solr/guide/config-api.html#config-api-endpoints
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SubPath
{
    public const GET_CONFIG = '';
    public const GET_OVERLAY = 'overlay';
    public const GET_SEARCH_COMPONENTS = 'searchComponent';
    public const GET_REQUEST_HANDLERS = 'requestHandler';
    public const GET_QUERY = 'query';
    public const GET_UPDATE_HANDLER = 'updateHandler';
    public const GET_REQUEST_DISPATCHER = 'requestDispatcher';

    /**
     * string[].
     */
    public const ALL = [
        self::GET_CONFIG,
        self::GET_OVERLAY,
        self::GET_SEARCH_COMPONENTS,
        self::GET_REQUEST_HANDLERS,
        self::GET_QUERY,
        self::GET_UPDATE_HANDLER,
        self::GET_REQUEST_DISPATCHER,
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
