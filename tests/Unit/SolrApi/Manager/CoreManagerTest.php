<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Manager;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\SolrApi\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\Response\CoreResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * CoreManagerTest.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class CoreManagerTest extends TestCase
{
    /**
     * test reload.
     */
    public function testReload(): void
    {
        $serializer = $this->getSerializer(1);
        $client = $this->getClient(['core' => 'foo', 'action' => 'RELOAD']);

        $manager = (new CoreManager($client, $serializer))->setCore('foo');

        self::assertInstanceOf(CoreResponse::class, $manager->reload());
    }

    /**
     * test status.
     */
    public function testStatus(): void
    {
        $serializer = $this->getSerializer(1);
        $client = $this->getClient(['core' => 'foo', 'action' => 'STATUS']);

        $manager = (new CoreManager($client, $serializer))->setCore('foo');

        self::assertInstanceOf(CoreResponse::class, $manager->status());
    }

    /**
     * test create.
     */
    public function testCreate(): void
    {
        $serializer = $this->getSerializer(1);
        $client = $this->getClient([
            'action' => 'CREATE',
            'name' => 'foo',
            'instanceDir' => '/var/solr/data/foo',
        ]);

        $manager = (new CoreManager($client, $serializer))->setCore('foo');

        self::assertInstanceOf(CoreResponse::class, $manager->create());
    }

    /**
     * test unload.
     */
    public function testUnload(): void
    {
        $serializer = $this->getSerializer(1);
        $client = $this->getClient([
            'core' => 'foo',
            'action' => 'UNLOAD',
            'deleteDataDir' => 'false',
        ]);

        $manager = (new CoreManager($client, $serializer))->setCore('foo');

        self::assertInstanceOf(CoreResponse::class, $manager->unload());
    }

    /**
     * test unload force.
     */
    public function testUnloadForce(): void
    {
        $serializer = $this->getSerializer(1);
        $client = $this->getClient([
            'core' => 'foo',
            'action' => 'UNLOAD',
            'deleteDataDir' => 'true',
        ]);

        $manager = (new CoreManager($client, $serializer))->setCore('foo');

        self::assertInstanceOf(CoreResponse::class, $manager->unload(true));
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
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\SerializerInterface
     */
    private function getSerializer(int $deserializeCount = null)
    {
        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializer
            ->expects(self::exactly($deserializeCount))
            ->method('deserialize')
            ->with('', CoreResponse::class, 'json')
            ->willReturn(new CoreResponse());

        return $serializer;
    }
}
