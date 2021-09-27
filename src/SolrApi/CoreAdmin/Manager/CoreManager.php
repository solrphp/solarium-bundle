<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use Solarium\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;

/**
 * Solr Collections Manager.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CoreManager
{
    private const ACTION_SWAP = 'SWAP';
    private const ACTION_SPLIT = 'SPLIT';
    private const ACTION_STATUS = 'STATUS';
    private const ACTION_CREATE = 'CREATE';
    private const ACTION_UNLOAD = 'UNLOAD';
    private const ACTION_RENAME = 'RENAME';
    private const ACTION_RELOAD = 'RELOAD';
    private const ACTION_MERGE_INDEXES = 'MERGEINDEXES';

    /**
     * @var string
     */
    public static string $handler = 'cores';

    /**
     * @var \Solarium\Client
     */
    private Client $client;

    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param \Solarium\Client                    $client
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param array<string, string|bool> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface|StatusResponse
     */
    public function status(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_STATUS, $options), StatusResponse::class);
    }

    /**
     * @param array<string, string> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function create(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_CREATE, $options));
    }

    /**
     * @param array<string, bool|string> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function unload(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_UNLOAD, $options));
    }

    /**
     * @param array<string, bool|string> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function rename(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_RENAME, $options));
    }

    /**
     * @param array<string, bool|string> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function swap(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_SWAP, $options));
    }

    /**
     * @param array<string, string|array<string>> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function mergeIndexes(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_MERGE_INDEXES, $options));
    }

    /**
     * @param array<string, string> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function reload(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_RELOAD, $options));
    }

    /**
     * @param array<string, string|array<string|array<string>>> $options
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function split(array $options = []): ResponseInterface
    {
        return $this->call($this->getRequest(self::ACTION_SPLIT, $options));
    }

    /**
     * @param \Solarium\Core\Client\Request $request
     * @param string|null                   $responseClass
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     *
     * @throws \JMS\Serializer\Exception\RuntimeException
     */
    private function call(Request $request, string $responseClass = null): ResponseInterface
    {
        $response = $this->client->executeRequest($request, $this->getEndpoint());

        return $this->serializer->deserialize($response->getBody(), ResponseInterface::class, 'json', $this->createContext($responseClass));
    }

    /**
     * the solr pre-deserialization event subscriber will modify data
     * and change type to the one set in the solrphp.real_class attribute.
     *
     * @param string|null $class
     *
     * @return \JMS\Serializer\DeserializationContext
     */
    private function createContext(?string $class): DeserializationContext
    {
        $realClass = is_subclass_of($class ?? '', ResponseInterface::class) ? $class : CoreResponse::class;

        return DeserializationContext::create()
            ->setAttribute('solrphp.real_class', $realClass)
        ;
    }

    /**
     * @return \Solarium\Core\Client\Endpoint
     */
    private function getEndpoint(): Endpoint
    {
        return new Endpoint(['collection' => 'admin']);
    }

    /**
     * @param string               $action
     * @param array<string, mixed> $options
     *
     * @return \Solarium\Core\Client\Request
     */
    private function getRequest(string $action, array $options = []): Request
    {
        return (new Request())
            ->setHandler(self::$handler)
            ->addParams(
                array_merge(
                    $options,
                    [
                        'action' => $action,
                    ]
                )
            );
    }
}
