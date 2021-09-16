<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Enum;

/**
 * Schema commands.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class Command
{
    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-new-field
     */
    public const ADD_FIELD = 'add-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-field
     */
    public const DELETE_FIELD = 'delete-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#replace-a-field
     */
    public const REPLACE_FIELD = 'replace-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-dynamic-field-rule
     */
    public const ADD_DYNAMIC_FIELD = 'add-dynamic-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-dynamic-field-rule
     */
    public const DELETE_DYNAMIC_FIELD = 'delete-dynamic-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#replace-a-dynamic-field-rule
     */
    public const REPLACE_DYNAMIC_FIELD = 'replace-dynamic-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-new-field-type
     */
    public const ADD_FIELD_TYPE = 'add-field-type';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-field-type
     */
    public const DELETE_FIELD_TYPE = 'delete-field-type';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#replace-a-field-type
     */
    public const REPLACE_FIELD_TYPE = 'replace-field-type';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-new-copy-field-rule
     */
    public const ADD_COPY_FIELD = 'add-copy-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-copy-field-rule
     */
    public const DELETE_COPY_FIELD = 'delete-copy-field';

    /**
     * Available commands for the schema api.
     */
    public const COMMANDS = [
        self::ADD_FIELD => [],
        self::DELETE_FIELD => [],
        self::REPLACE_FIELD => [],
        self::ADD_DYNAMIC_FIELD => [],
        self::DELETE_DYNAMIC_FIELD => [],
        self::REPLACE_DYNAMIC_FIELD => [],
        self::ADD_FIELD_TYPE => [],
        self::DELETE_FIELD_TYPE => [],
        self::REPLACE_FIELD_TYPE => [],
        self::ADD_COPY_FIELD => [],
        self::DELETE_COPY_FIELD => [],
    ];

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
