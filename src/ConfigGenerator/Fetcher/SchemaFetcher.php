<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Fetcher;

use Solarium\Client;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\FetcherInterface;

/**
 * Schema Fetcher.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SchemaFetcher implements FetcherInterface
{
    /**
     * @var \Solarium\Client
     */
    private Client $client;

    /**
     * @param \Solarium\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchXml(string $core): string
    {
        $query = $this->client->createApi(
            [
                'handler' => sprintf('%s/schema', $core),
            ]
        )->addParam('wt', 'schema.xml');

        return $this->client->execute($query, $core)->getResponse()->getBody();
    }
}
