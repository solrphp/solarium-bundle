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
 * Params Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class Command
{
    /**
     * @see https://solr.apache.org/guide/request-parameters-api.html#configuring-request-parameters
     */
    public const SET_PARAM = 'set';

    /**
     * @see https://solr.apache.org/guide/request-parameters-api.html#configuring-request-parameters
     */
    public const DELETE_PARAM = 'delete';

    /**
     * @see https://solr.apache.org/guide/request-parameters-api.html#configuring-request-parameters
     */
    public const UPDATE_PARAM = 'update';

    /**
     * all commands.
     */
    public const ALL = [
        self::SET_PARAM => [],
        self::DELETE_PARAM => [],
        self::UPDATE_PARAM => [],
    ];

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
