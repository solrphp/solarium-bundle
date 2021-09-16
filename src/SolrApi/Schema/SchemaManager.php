<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema;

use Solrphp\SolariumBundle\Manager\AbstractApiManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command as SchemaCommands;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath as SchemaSubPaths;

/**
 * Solr Schema Manager.
 *
 * @see https://lucene.apache.org/solr/guide/8_4/schema-api.html
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SchemaManager extends AbstractApiManager
{
    /**
     * {@inheritdoc}
     */
    protected static array $availableCommands = SchemaCommands::COMMANDS;

    /**
     * {@inheritdoc}
     */
    protected static array $availableSubPaths = SchemaSubPaths::SUB_PATHS;

    /**
     * {@inheritdoc}
     */
    protected static string $handler = 'schema';

    /**
     * @param string $subPath
     *
     * @return \Solrphp\SolariumBundle\Response\AbstractResponse|\Solarium\Core\Client\Response
     */
    public function call(string $subPath)
    {
        $response = parent::call($subPath);

        if (false === \array_key_exists($subPath, SchemaSubPaths::RESPONSE_CLASSES)) {
            return $response;
        }

        return $this->serializer->deserialize($response->getBody(), SchemaSubPaths::RESPONSE_CLASSES[$subPath], 'json');
    }
}
