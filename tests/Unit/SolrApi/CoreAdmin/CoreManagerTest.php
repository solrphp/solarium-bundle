<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\CoreAdmin;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;
use Symfony\Component\Serializer\SerializerInterface;

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
        $serializer = $this->getSerializer(1);
        $client = $this->getClient($options = ['core' => 'foo', 'action' => 'RELOAD']);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(CoreResponse::class, $manager->reload($options));
    }

    /**
     * test status.
     */
    public function testStatus(): void
    {
        $serializer = $this->getSerializer(1, StatusResponse::class);
        $client = $this->getClient(['action' => 'STATUS']);

        $manager = (new CoreManager($client, $serializer));

        self::assertInstanceOf(StatusResponse::class, $manager->status());
    }

    /**
     * test create.
     */
    public function testCreate(): void
    {
        $serializer = $this->getSerializer(1);

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
        $serializer = $this->getSerializer(1);

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
        $serializer = $this->getSerializer(1);
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
        $serializer = $this->getSerializer(1);
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
        $serializer = $this->getSerializer(1);
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
        $serializer = $this->getSerializer(1);
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

        $serializer = $this->getSerializer(1);
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
                ->willReturn(new Response(''))
            ;
        }

        return $client;
    }

    /**
     * @param int|null $deserializeCount
     * @param string   $responseClass
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\SerializerInterface
     *
     * @throws \PHPUnit\Framework\Exception
     */
    private function getSerializer(int $deserializeCount = null, string $responseClass = CoreResponse::class)
    {
        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializer
            ->expects(self::exactly($deserializeCount))
            ->method('deserialize')
            ->with('', $responseClass, 'json')
            ->willReturn(new $responseClass());

        return $serializer;
    }
}
