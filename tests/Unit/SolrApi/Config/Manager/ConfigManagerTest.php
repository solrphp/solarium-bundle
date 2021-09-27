<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Manager;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\QueryType;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Server\Api\Query;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath as SubPathConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Config Manager Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
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
        $serializer = new SolrSerializer();

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
            'version' => null,
            'method' => Request::METHOD_GET,
            'resultclass' => QueryType::class,
            'handler' => 'foo/config/'.SubPathConfig::GET_OVERLAY,
        ];

        $client = $this->getExecutingClient($options);
        $serializer = new SolrSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $configManager = new ConfigManager($client, $coreManager, $serializer);
        $configManager->setCore('foo');

        $response = $configManager->call(SubPathConfig::GET_OVERLAY);

        self::assertInstanceOf(ConfigResponse::class, $response);
        self::assertSame('lorem ipsum', $response->getBody());
    }

    /**
     * test custom response class.
     */
    public function testCustomResponseClass(): void
    {
        $options = [
            'version' => null,
            'method' => Request::METHOD_GET,
            'resultclass' => QueryType::class,
            'handler' => 'foo/config/'.SubPathConfig::GET_REQUEST_HANDLERS,
        ];

        $client = $this->getExecutingClient($options);
        $serializer = new SolrSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $configManager = new ConfigManager($client, $coreManager, $serializer);
        $configManager->setCore('foo');

        $response = $configManager->call(SubPathConfig::GET_REQUEST_HANDLERS);

        self::assertInstanceOf(ConfigResponse::class, $response);
        self::assertSame('lorem ipsum', $response->getBody());
    }

    /**
     * test non existing command.
     */
    public function testNonExistingCommand(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $client = $this->getExecutingClient([]);
        $serializer = new SolrSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $schemaManager = new ConfigManager($client, $coreManager, $serializer);

        $schemaManager->addCommand('foo', new Field());
    }

    /**
     * test add command.
     */
    public function testAddCommand(): void
    {
        $client = $this->getExecutingClient([]);
        $serializer = new SolrSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $configManager = new ConfigManager($client, $coreManager, $serializer);

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
        $serializer = new SolrSerializer();

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
            'version' => null,
            'method' => Request::METHOD_POST,
            'resultclass' => QueryType::class,
            'contenttype' => 'application/json',
            'handler' => 'foo/config',
            'rawdata' => '{"set-property":[{"foo":"bar"}]}',
        ];

        $serializer = new SolrSerializer();
        $client = $this->getExecutingClient($options);

        $coreManager = new CoreManager($client, $serializer);
        $configManager = (new ConfigManager($client, $coreManager, $serializer))->setCore('foo');
        $configManager->addCommand(Command::SET_PROPERTY, new Property('foo', 'bar'));

        $response = $configManager->persist();

        self::assertInstanceOf(Result::class, $response);
    }

    /**
     * @throws \JsonException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyPersist(): void
    {
        $serializer = new SolrSerializer();
        $client = $this->getExecutingClient();
        $coreManager = new CoreManager($client, $serializer);
        $configManager = (new ConfigManager($client, $coreManager, $serializer))->setCore('foo');

        self::assertNull($configManager->persist());
    }

    /**
     * @param array $options
     * @param array $params
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Solarium\Client
     */
    private function getRequestingClient(array $options = [], array $params = [])
    {
        $response = new Response('{"body":"lorem ipsum"}', ['HTTP 200 OK']);

        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([new Curl(), new EventDispatcher()])
            ->onlyMethods(['executeRequest'])
            ->getMock()
        ;

        $client->expects(self::once())
            ->method('executeRequest')
            ->with(
                self::callback(static function ($request) use ($options, $params) {
                    return $options === $request->getOptions() && $params === $request->getParams();
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
        $response = new Response('{"body":"lorem ipsum"}', ['HTTP 200 OK']);
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
}
