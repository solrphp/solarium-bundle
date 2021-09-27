<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\CoreAdmin\Manager;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;

/**
 * CoreManagerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CoreManagerTest extends TestCase
{
    /**
     * test reload.
     */
    public function testReload(): void
    {
        $serializer = new SolrSerializer();
        $client = $this->getClient($options = ['core' => 'foo', 'action' => 'RELOAD']);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->reload($options));
    }

    /**
     * test status.
     */
    public function testStatus(): void
    {
        $serializer = new SolrSerializer();
        $client = $this->getClient(['action' => 'STATUS']);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(StatusResponse::class, $manager->status());
    }

    /**
     * test create.
     */
    public function testCreate(): void
    {
        $serializer = new SolrSerializer();

        $options = [
            'core' => 'foo',
            'instanceDir' => '/var/solr/data/foo',
            'action' => 'CREATE',
        ];

        $client = $this->getClient($options);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->create($options));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRename(): void
    {
        $serializer = new SolrSerializer();

        $options = [
            'core' => 'foo',
            'other' => 'bar',
            'action' => 'RENAME',
        ];

        $client = $this->getClient($options);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->rename($options));
    }

    /**
     * test unload.
     */
    public function testUnload(): void
    {
        $options = [
            'core' => 'foo',
            'action' => 'UNLOAD',
        ];
        $serializer = new SolrSerializer();
        $client = $this->getClient($options);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->unload($options));
    }

    /**
     * test unload.
     */
    public function testSwap(): void
    {
        $options = [
            'core' => 'foo',
            'other' => 'bar',
            'action' => 'SWAP',
        ];
        $serializer = new SolrSerializer();
        $client = $this->getClient($options);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->swap($options));
    }

    /**
     * test merge index.
     */
    public function testMergeIndex(): void
    {
        $options = [
            'core' => 'foo',
            'indexDir' => 'bar',
            'action' => 'MERGEINDEXES',
        ];
        $serializer = new SolrSerializer();
        $client = $this->getClient($options);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->mergeIndexes($options));
    }

    /**
     * test split.
     */
    public function testSplit(): void
    {
        $options = [
            'core' => 'foo',
            'path' => 'bar',
            'action' => 'SPLIT',
        ];
        $serializer = new SolrSerializer();
        $client = $this->getClient($options);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->split($options));
    }

    /**
     * test unload force.
     */
    public function testUnloadForce(): void
    {
        $options = [
            'core' => 'foo',
            'deleteDataDir' => 'true',
            'action' => 'UNLOAD',
        ];

        $serializer = new SolrSerializer();
        $client = $this->getClient($options);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->unload($options));
    }

    /**
     * @param array|null $requestOptions
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Solarium\Client
     */
    private function getClient(array $requestOptions = null)
    {
        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();

        if (null !== $requestOptions) {
            $client
                ->expects(self::once())
                ->method('executeRequest')
                ->with(
                    self::callback(function ($request) use ($requestOptions) {
                        return $request->getParams() === $requestOptions && 'cores' === $request->getHandler();
                    }),
                    self::callback(function ($endpoint) {
                        return 'admin' === $endpoint->getCollection();
                    })
                )
                ->willReturn(new Response('{"body":"lorem ipsum"}'))
            ;
        }

        return $client;
    }
}
