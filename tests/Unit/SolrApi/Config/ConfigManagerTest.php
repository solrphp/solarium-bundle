<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\QueryType;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Server\Api\Query;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath as SubPathConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Config Manager Test.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class ConfigManagerTest extends TestCase
{
    /**
     * test Undefined sub path.
     */
    public function testUndefinedSubPath(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $client = $this->getExecutingClient([]);
        $serializer = $this->getSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $configManager = new ConfigManager($client, $coreManager, $serializer);

        $configManager->call('foo');
    }

    /**
     * test no custom response class.
     */
    public function testNoCustomResponseClass(): void
    {
        $options = [
            'version' => Request::API_V2,
            'method' => Request::METHOD_GET,
            'resultclass' => QueryType::class,
            'handler' => 'cores/foo/config/'.SubPathConfig::GET_OVERLAY,
        ];

        $client = $this->getExecutingClient($options);
        $serializer = $this->getSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $configManager = new ConfigManager($client, $coreManager, $serializer);
        $configManager->setCore('foo');

        $response = $configManager->call(SubPathConfig::GET_OVERLAY);

        self::assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * test custom response class.
     */
    public function testCustomResponseClass(): void
    {
        $options = [
            'version' => Request::API_V2,
            'method' => Request::METHOD_GET,
            'resultclass' => QueryType::class,
            'handler' => 'cores/foo/config/'.SubPathConfig::GET_REQUEST_HANDLERS,
        ];

        $client = $this->getExecutingClient($options);
        $serializer = $this->getSerializer(1, '', ConfigResponse::class);

        $coreManager = new CoreManager($client, $serializer);
        $configManager = new ConfigManager($client, $coreManager, $serializer);
        $configManager->setCore('foo');

        $response = $configManager->call(SubPathConfig::GET_REQUEST_HANDLERS);

        self::assertInstanceOf(ConfigResponse::class, $response);
    }

    /**
     * test non existing command.
     */
    public function testNonExistingCommand(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $client = $this->getExecutingClient([]);
        $serializer = $this->getSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $schemaManager = new \Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager($client, $coreManager, $serializer);

        $schemaManager->addCommand('foo', new Field());
    }

    /**
     * test add command.
     */
    public function testAddCommand(): void
    {
        $client = $this->getExecutingClient([]);
        $serializer = $this->getSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $configManager = new \Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager($client, $coreManager, $serializer);

        $configManager = $configManager->addCommand(Command::ADD_INIT_PARAMS, new Field());

        self::assertInstanceOf(ConfigManager::class, $configManager);
    }

    /**
     * test flush.
     */
    public function testFlush(): void
    {
        $client = $this->getRequestingClient(
            [
                'method' => Request::METHOD_GET,
                'api' => Request::API_V1,
                'handler' => 'cores',
            ],
            [
                'core' => 'foo',
                'action' => 'RELOAD',
            ]
        );
        $serializer = $this->getSerializer(1, '', CoreResponse::class);

        $coreManager = new CoreManager($client, $serializer);
        $configManager = (new ConfigManager($client, $coreManager, $serializer))->setCore('foo');

        $response = $configManager->flush();

        self::assertInstanceOf(CoreResponse::class, $response);
    }

    /**
     * @throws \JsonException
     */
    public function testPersist(): void
    {
        $options = [
            'version' => Request::API_V2,
            'method' => Request::METHOD_POST,
            'resultclass' => QueryType::class,
            'contenttype' => 'application/json',
            'handler' => 'cores/foo/config',
            'rawdata' => '[]',
        ];

        $serializer = $this->getSerializer();
        $client = $this->getExecutingClient($options);

        $coreManager = new CoreManager($client, $serializer);
        $configManager = (new ConfigManager($client, $coreManager, $serializer))->setCore('foo');

        $response = $configManager->persist();

        self::assertInstanceOf(Result::class, $response);
    }

    /**
     * @param array $options
     * @param array $params
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Solarium\Client
     */
    private function getRequestingClient(array $options = [], array $params = [])
    {
        $response = new Response('', ['HTTP 200 OK']);

        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([new Curl(), new EventDispatcher()])
            ->onlyMethods(['executeRequest'])
            ->getMock()
        ;

        $client->expects(self::once())
            ->method('executeRequest')
            ->with(
                self::callback(static function ($request) use ($options, $params) {
                    return $options === $request->getOptions() && $params = $request->getParams();
                }),
                self::callback(static function ($endpoint) {
                    return 'admin' === $endpoint->getCollection();
                })
            )
            ->willReturn($response)
        ;

        return $client;
    }

    /**
     * @param array $options
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Solarium\Client
     */
    private function getExecutingClient(array $options = [])
    {
        $response = new Response('', ['HTTP 200 OK']);
        $result = new Result(new Query(), $response);

        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([new Curl(), new EventDispatcher()])
            ->onlyMethods(['execute'])
            ->getMock()
        ;

        if (0 === \count($options)) {
            return $client;
        }

        $client->expects(self::once())
            ->method('execute')
            ->with(
                self::callback(static function ($query) use ($options) {
                    return $options === $query->getOptions();
                }),
                self::callback(static function ($core) {
                    return 'foo' === $core;
                })
            )
            ->willReturn($result)
        ;

        return $client;
    }

    /**
     * @param int         $deserializeCount
     * @param string|null $responseData
     * @param string      $responseClass
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\SerializerInterface
     */
    private function getSerializer(int $deserializeCount = 0, string $responseData = null, string $responseClass = \stdClass::class)
    {
        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();

        $serializer
            ->expects(self::exactly($deserializeCount))
            ->method('deserialize')
            ->with($responseData, $responseClass, 'json')
            ->willReturn(new $responseClass());

        return $serializer;
    }
}
