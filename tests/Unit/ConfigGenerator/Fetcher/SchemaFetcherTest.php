<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Fetcher;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solrphp\SolariumBundle\ConfigGenerator\Fetcher\SchemaFetcher;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * SchemaFetcher Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaFetcherTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testFetch(): void
    {
        $options = [
            'version' => 'v1',
            'method' => 'GET',
            'resultclass' => 'Solarium\Core\Query\Result\QueryType',
            'handler' => 'foo/schema',
        ];

        $params = [
            'wt' => 'schema.xml',
        ];

        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([new Curl(), new EventDispatcher()])
            ->onlyMethods(['execute'])
            ->getMock()
        ;

        $client->expects(self::once())
            ->method('execute')
            ->with(
                self::callback(static function ($query) use ($options, $params) {
                    return $options === $query->getOptions() && $params === $query->getParams();
                }),
                self::callback(static function ($core) {
                    return 'foo' === $core;
                })
            )
        ;

        (new SchemaFetcher($client))->fetchXml('foo');
    }
}
