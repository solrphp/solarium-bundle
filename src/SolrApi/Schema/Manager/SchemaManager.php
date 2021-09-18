<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Manager;

use Solrphp\SolariumBundle\Common\Manager\AbstractApiManager;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command as SchemaCommands;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath as SchemaSubPaths;

/**
 * Solr Schema Manager.
 *
 * @see https://lucene.apache.org/solr/guide/schema-api.html
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SchemaManager extends AbstractApiManager
{
    /**
     * {@inheritdoc}
     */
    protected static array $availableCommands = SchemaCommands::ALL;

    /**
     * {@inheritdoc}
     */
    protected static array $availableSubPaths = SchemaSubPaths::ALL;

    /**
     * {@inheritdoc}
     */
    protected static string $handler = 'schema';

    /**
     * {@inheritdoc}
     */
    public function call(string $path): ResponseInterface
    {
        $response = parent::call($path);

        if (false === \array_key_exists($path, SchemaSubPaths::RESPONSE_CLASSES)) {
            return $response;
        }

        return $this->serializer->deserialize($response->getBody(), SchemaSubPaths::RESPONSE_CLASSES[$path], 'json');
    }
}
