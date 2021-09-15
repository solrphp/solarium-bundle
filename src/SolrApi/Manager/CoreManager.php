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
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse;
use Solrphp\SolariumBundle\SolrApi\Response\CoreResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Solr Collections Manager.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class CoreManager
{
    private const SOLR_HOME = '/var/solr/data';

    /**
     * @var \Solarium\Client
     */
    private Client $client;

    /**
     * @var string
     */
    private string $core;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param \Solarium\Client                                  $client
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     */
    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param string $core
     *
     * @return $this
     */
    public function setCore(string $core): self
    {
        $this->core = $core;

        return $this;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse
     */
    public function status(): AbstractResponse
    {
        $request = $this
            ->prepare()
            ->addParam('action', 'STATUS')
        ;

        return $this->call($request);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse
     */
    public function create(): AbstractResponse
    {
        $path = sprintf('%s/%s', self::SOLR_HOME, $this->core);

        $request = new Request();

        $request
            ->setHandler('cores')
            ->addParam('action', 'CREATE')
            ->addParam('name', $this->core)
            ->addParam('instanceDir', $path)
        ;

        return $this->call($request);
    }

    /**
     * @param bool $force
     *
     * @return \Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse
     */
    public function unload(bool $force = false): AbstractResponse
    {
        $request = $this
            ->prepare()
            ->addParams([
                'action' => 'UNLOAD',
                'deleteDataDir' => $force,
            ])
        ;

        return $this->call($request);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse
     */
    public function reload(): AbstractResponse
    {
        $request = $this
            ->prepare()
            ->addParam('action', 'RELOAD')
        ;

        return $this->call($request);
    }

    /**
     * @param \Solarium\Core\Client\Request $request
     *
     * @return \Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse
     */
    private function call(Request $request): AbstractResponse
    {
        $response = $this->client->executeRequest($request, $this->getEndpoint());

        return $this->serializer->deserialize($response->getBody(), CoreResponse::class, 'json');
    }

    /**
     * @return \Solarium\Core\Client\Endpoint
     */
    private function getEndpoint(): Endpoint
    {
        return new Endpoint(['collection' => 'admin']);
    }

    /**
     * @return \Solarium\Core\Client\Request
     */
    private function prepare(): Request
    {
        $request = new Request();

        $request->setHandler('cores');
        $request->addParam('core', $this->core);

        return $request;
    }
}
