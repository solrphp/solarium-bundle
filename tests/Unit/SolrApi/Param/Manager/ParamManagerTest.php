<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Manager;

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
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\SubPath as SubPathParam;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamManager;
use Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * ParamManagerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamManagerTest extends TestCase
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
        $paramManager = new ParamManager($client, $coreManager, $serializer);

        $paramManager->call('foo');
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
            'handler' => 'foo/config/params',
        ];

        $client = $this->getExecutingClient($options);
        $serializer = new SolrSerializer();
        $coreManager = new CoreManager($client, $serializer);
        $paramManager = new ParamManager($client, $coreManager, $serializer);
        $paramManager
            ->setCore('foo');

        $response = $paramManager->call(SubPathParam::LIST_PARAMS);

        self::assertInstanceOf(ParamResponse::class, $response);
        self::assertSame('lorem ipsum', $response->getBody());
    }

    /**
     * test non-existing command.
     */
    public function testNonExistingCommand(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $client = $this->getExecutingClient([]);
        $serializer = new SolrSerializer();
        $coreManager = new CoreManager($client, $serializer);
        $paramManager = new ParamManager($client, $coreManager, $serializer);
        $paramManager->addCommand('foo', new Field());
    }

    /**
     * test add command.
     */
    public function testAddCommand(): void
    {
        $client = $this->getExecutingClient([]);
        $serializer = new SolrSerializer();
        $coreManager = new CoreManager($client, $serializer);
        $paramManager = new ParamManager($client, $coreManager, $serializer);

        $paramManager = $paramManager->addCommand(Command::SET_PARAM, new ParameterSetMap());

        self::assertInstanceOf(ParamManager::class, $paramManager);
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
        $paramManager = (new ParamManager($client, $coreManager, $serializer))->setCore('foo');

        $response = $paramManager->flush();

        self::assertInstanceOf(CoreResponse::class, $response);
    }

    /**
     * @throws \JsonException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\HttpException
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    public function testPersistDelete(): void
    {
        $setMapOne = new ParameterSetMap();
        $setMapOne->setName('foo');
        $setMapTwo = new ParameterSetMap();
        $setMapTwo->setName('bar');

        $options = [
            'version' => null,
            'method' => Request::METHOD_POST,
            'resultclass' => QueryType::class,
            'contenttype' => 'application/json',
            'handler' => 'foo/config/params',
            'rawdata' => '{"delete":["foo"],"delete":["bar"]}',
        ];

        $client = $this->getExecutingClient($options);
        $serializer = new SolrSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $paramManager = (new ParamManager($client, $coreManager, $serializer))->setCore('foo');

        $paramManager->addCommand(Command::DELETE_PARAM, $setMapOne);
        $paramManager->addCommand(Command::DELETE_PARAM, $setMapTwo);

        $response = $paramManager->persist();

        self::assertInstanceOf(Result::class, $response);
    }

    /**
     * @throws \JsonException
     */
    public function testPersist(): void
    {
        $setMap = new ParameterSetMap();
        $setMap->setName('foo');
        $param = new Parameter('foo', 'bar');
        $setMap->addParameter($param);
        $setMap->addAppend($param);
        $setMap->addInvariant($param);

        $options = [
            'version' => null,
            'method' => Request::METHOD_POST,
            'resultclass' => QueryType::class,
            'contenttype' => 'application/json',
            'handler' => 'foo/config/params',
            'rawdata' => '{"set":{"foo":{"foo":"bar","_invariants_":{"foo":"bar"},"_appends_":{"foo":"bar"}}},"set":{"foo":{"foo":"bar","_invariants_":{"foo":"bar"},"_appends_":{"foo":"bar"}}}}',
        ];

        $client = $this->getExecutingClient($options);
        $serializer = new SolrSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $paramManager = (new ParamManager($client, $coreManager, $serializer))->setCore('foo');

        $paramManager->addCommand(Command::SET_PARAM, $setMap);
        $paramManager->addCommand(Command::SET_PARAM, $setMap);

        $response = $paramManager->persist();

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
     * @param array<string> $options
     * @param string        $body
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Solarium\Client
     *
     * @throws \Solarium\Exception\HttpException
     */
    private function getExecutingClient(array $options = [], string $body = '{"body":"lorem ipsum"}')
    {
        $response = new Response($body, ['HTTP 200 OK']);
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
