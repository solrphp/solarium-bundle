<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Manager;

use JMS\Serializer\SerializerInterface;
use Solarium\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Server\Api\Query;
use Solrphp\SolariumBundle\Common\Collection\CommandCollection;
use Solrphp\SolariumBundle\Common\Response\RawSolrApiResponse;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;

/**
 * Abstract Solr Api Manager.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
abstract class AbstractApiManager implements SolrApiManagerInterface
{
    /**
     * Available commands for API v2 endpoint.
     *
     * @var array<string, array<\JsonSerializable>|null>
     */
    protected static array $availableCommands = [];

    /**
     * Available sub paths for API v2 endpoint.
     *
     * @var array<int, string>
     */
    protected static array $availableSubPaths = [];

    /**
     * API v2 handler.
     *
     * @var string
     */
    protected static string $handler = '';

    /**
     * api version or null for non-prefixed calls (config v1 calls).
     *
     * @var string|null
     */
    protected static ?string $api;

    /**
     * @var \Solarium\Core\Client\Client
     */
    protected $client;

    /**
     * Solr API v2 query.
     *
     * @var \Solarium\QueryType\Server\Api\Query
     */
    protected Query $query;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager
     */
    protected CoreManager $coreManager;

    /**
     * @var string
     */
    protected string $core;

    /**
     * @var \Solrphp\SolariumBundle\Common\Collection\CommandCollection
     */
    protected CommandCollection $commands;

    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param \Solarium\Client                                              $client
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager $coreManager
     * @param \JMS\Serializer\SerializerInterface                           $serializer
     */
    public function __construct(Client $client, CoreManager $coreManager, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->coreManager = $coreManager;
        $this->serializer = $serializer;

        $this->commands = new CommandCollection(static::$availableCommands);
    }

    /**
     * {@inheritdoc}
     */
    public function setCore(string $core): self
    {
        $this->core = $core;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCommand(string $command, \JsonSerializable $data): self
    {
        if (false === \array_key_exists($command, static::$availableCommands)) {
            throw new UnexpectedValueException(sprintf('unknown command: %s. available commands: %s', $command, implode(', ', array_keys(static::$availableCommands))));
        }

        $this->commands->add($command, $data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(): ResultInterface
    {
        $query = $this->client
            ->createApi([
                'version' => static::$api,
                'method' => Request::METHOD_POST,
                'contenttype' => 'application/json',
                'handler' => $this->getHandler(),
            ]);

        $query->setOptions(['rawdata' => json_encode($this->commands, \JSON_THROW_ON_ERROR)], false);

        return $this->client->execute($query, $this->core);
    }

    /**
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function flush(): ResponseInterface
    {
        return $this->coreManager->reload(['core' => $this->core]);
    }

    /**
     * {@inheritdoc}
     */
    public function call(string $path): ResponseInterface
    {
        if (false === \in_array($path, static::$availableSubPaths, true)) {
            throw new UnexpectedValueException(sprintf('unknown sub path: %s. available sub paths: %s', $path, implode(', ', static::$availableSubPaths)));
        }

        // todo: use setters once typehints are fixed
        $query = $this->client
            ->createApi([
                'version' => static::$api,
                'method' => Request::METHOD_GET,
                'handler' => sprintf('%s/%s', $this->getHandler(), $path),
            ]);

        $response = $this->client->execute($query, $this->core)->getResponse();

        return new RawSolrApiResponse($response->getBody());
    }

    /**
     * @return string
     */
    private function getHandler(): string
    {
        return sprintf('%s/%s', $this->core, static::$handler);
    }
}
