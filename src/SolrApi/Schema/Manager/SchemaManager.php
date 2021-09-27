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

use JMS\Serializer\DeserializationContext;
use Solarium\Core\Client\Request;
use Solrphp\SolariumBundle\Common\Manager\AbstractApiManager;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command as SchemaCommands;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath as SchemaSubPaths;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\SchemaResponse;

/**
 * Solr Schema Manager.
 *
 * @see https://lucene.apache.org/solr/guide/schema-api.html
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaManager extends AbstractApiManager
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
     * api version or null for non-prefixed calls (config v1 calls).
     *
     * @var string|null
     */
    protected static ?string $api = Request::API_V1;

    /**
     * {@inheritdoc}
     */
    public function call(string $path): ResponseInterface
    {
        $response = parent::call($path);

        return $this->serializer->deserialize($response->getBody() ?? '{}', ResponseInterface::class, 'json', $this->createContext($path));
    }

    /**
     * the solr pre-deserialization event subscriber will modify data
     * and change type to the one set in the solrphp.real_class attribute.
     *
     * @param string $path
     *
     * @return \JMS\Serializer\DeserializationContext
     */
    private function createContext(string $path): DeserializationContext
    {
        return DeserializationContext::create()
            ->setAttribute('solrphp.real_class', SchemaSubPaths::RESPONSE_CLASSES[$path] ?? SchemaResponse::class)
        ;
    }
}
