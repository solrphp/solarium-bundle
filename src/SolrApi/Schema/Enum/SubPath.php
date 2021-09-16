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

use Solrphp\SolariumBundle\SolrApi\Schema\Response\CopyFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\DynamicFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldsResponse;

/**
 * Schema API sub paths.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SubPath
{
    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#retrieve-the-entire-schema
     */
    public const GET_SCHEMA = '';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-fields
     */
    public const LIST_FIELDS = 'fields';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-fields
     */
    public const LIST_FIELD = 'fields/%s';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-dynamic-fields
     */
    public const LIST_DYNAMIC_FIELDS = 'dynamicfields';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-dynamic-fields
     */
    public const LIST_DYNAMIC_FIELD = 'dynamicfields/%s';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-field-types
     */
    public const LIST_FIELD_TYPES = 'fieldtypes';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-field-types
     */
    public const LIST_FIELD_TYPE = 'fieldtypes/%s';

    /**
     * @ssee https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-copy-fields
     */
    public const LIST_COPY_FIELDS = 'copyfields';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#show-schema-name
     */
    public const SHOW_SCHEMA_NAME = 'name';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#show-the-schema-version
     */
    public const SHOW_SCHEMA_VERSION = 'version';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#list-uniquekey
     */
    public const LIST_UNIQUE_KEY = 'uniquekey';

    /**
     * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html#show-global-similarity
     */
    public const SHOW_GLOBAL_SIMILARITY = 'similarity';

    public const SUB_PATHS = [
        self::GET_SCHEMA,
        self::LIST_FIELDS,
        self::LIST_FIELD,
        self::LIST_DYNAMIC_FIELDS,
        self::LIST_DYNAMIC_FIELD,
        self::LIST_FIELD_TYPES,
        self::LIST_FIELD_TYPE,
        self::LIST_COPY_FIELDS,
        self::SHOW_SCHEMA_NAME,
        self::SHOW_SCHEMA_VERSION,
        self::LIST_UNIQUE_KEY,
        self::SHOW_GLOBAL_SIMILARITY,
    ];

    public const RESPONSE_CLASSES = [
        self::LIST_FIELDS => FieldsResponse::class,
        self::LIST_COPY_FIELDS => CopyFieldsResponse::class,
        self::LIST_DYNAMIC_FIELDS => DynamicFieldsResponse::class,
    ];

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
