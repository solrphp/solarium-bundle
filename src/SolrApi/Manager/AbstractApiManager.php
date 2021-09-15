<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Manager;

use Solarium\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Server\Api\Query;
use Solrphp\SolariumBundle\SolrApi\Command\CommandCollection;
use Solrphp\SolariumBundle\SolrApi\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Abstract Solr Api Manager.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
abstract class AbstractApiManager
{
    /**
     * Available commands for API v2 endpoint.
     *
     * @var array<string, array>
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
     * @var \Solrphp\SolariumBundle\SolrApi\Manager\CoreManager
     */
    protected CoreManager $coreManager;

    /**
     * @var string
     */
    protected string $core;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Command\CommandCollection
     */
    protected CommandCollection $commands;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param \Solarium\Client                                    $client
     * @param \Solrphp\SolariumBundle\SolrApi\Manager\CoreManager $coreManager
     * @param \Symfony\Component\Serializer\SerializerInterface   $serializer
     */
    public function __construct(Client $client, CoreManager $coreManager, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->coreManager = $coreManager;
        $this->serializer = $serializer;

        $this->commands = new CommandCollection(static::$availableCommands);
    }

    /**
     * @param string $core
     *
     * @return static
     */
    public function setCore(string $core): self
    {
        $this->core = $core;
        $this->coreManager->setCore($core);

        return $this;
    }

    /**
     * @param string            $command
     * @param \JsonSerializable $data
     *
     * @return static
     *
     * @throws \Solrphp\SolariumBundle\SolrApi\Exception\UnexpectedValueException
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
     * @return \Solarium\Core\Query\Result\ResultInterface
     */
    public function persist(): ResultInterface
    {
        $query = $this->client->createApi()
            ->setVersion(Request::API_V2)
            ->setMethod(Request::METHOD_POST)
            ->setContentType('application/json')
            ->setHandler($this->getHandler())
        ;

        $query->setOptions(['rawdata' => json_encode($this->commands, \JSON_THROW_ON_ERROR)], false);

        return $this->client->execute($query, $this->core);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse
     */
    public function flush(): AbstractResponse
    {
        return $this->coreManager->reload();
    }

    /**
     * @param string $subPath
     *
     * @return \Solarium\Core\Client\Response
     *
     * @throws \Solrphp\SolariumBundle\SolrApi\Exception\UnexpectedValueException
     */
    protected function call(string $subPath)
    {
        if (false === \in_array($subPath, static::$availableSubPaths, true)) {
            throw new UnexpectedValueException(sprintf('unknown sub path: %s. available sub paths: %s', $subPath, implode(', ', static::$availableSubPaths)));
        }

        // todo: use setters once typehints are fixed
        $query = $this->client
            ->createApi([
                'version' => Request::API_V2,
                'method' => Request::METHOD_GET,
                'handler' => sprintf('%s/%s', $this->getHandler(), $subPath),
            ])
        ;

        return $this->client->execute($query, $this->core)->getResponse();
    }

    /**
     * @return string
     */
    private function getHandler(): string
    {
        return sprintf('cores/%s/%s', $this->core, static::$handler);
    }
}
