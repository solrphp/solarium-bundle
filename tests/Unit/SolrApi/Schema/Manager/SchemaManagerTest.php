<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Manager;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\QueryType;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Server\Api\Query;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath as SubPathSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\SchemaResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Schema Manager Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaManagerTest extends TestCase
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
        $schemaManager = new SchemaManager($client, $coreManager, $serializer);

        $schemaManager->call('foo');
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
            'handler' => 'foo/schema/'.SubPathSchema::SHOW_GLOBAL_SIMILARITY,
        ];

        $client = $this->getExecutingClient($options);
        $serializer = $this->getSerializer(1, '', SchemaResponse::class);
        $coreManager = new CoreManager($client, $serializer);
        $schemaManager = new SchemaManager($client, $coreManager, $serializer);
        $schemaManager
            ->setCore('foo');

        $response = $schemaManager->call(SubPathSchema::SHOW_GLOBAL_SIMILARITY);

        self::assertInstanceOf(SchemaResponse::class, $response);
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
            'handler' => 'foo/schema/'.SubPathSchema::LIST_FIELDS,
        ];

        $client = $this->getExecutingClient($options);
        $serializer = $this->getSerializer(1, '', FieldsResponse::class);
        $coreManager = new CoreManager($client, $serializer);
        $schemaManager = new SchemaManager($client, $coreManager, $serializer);
        $schemaManager
            ->setCore('foo');

        $response = $schemaManager->call(SubPathSchema::LIST_FIELDS);

        self::assertInstanceOf(FieldsResponse::class, $response);
    }

    /**
     * test non-existing command.
     */
    public function testNonExistingCommand(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $client = $this->getExecutingClient([]);
        $serializer = $this->getSerializer();
        $coreManager = new CoreManager($client, $serializer);
        $schemaManager = new SchemaManager($client, $coreManager, $serializer);
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
        $schemaManager = new SchemaManager($client, $coreManager, $serializer);

        $schemaManager = $schemaManager->addCommand(Command::ADD_COPY_FIELD, new Field());

        self::assertInstanceOf(SchemaManager::class, $schemaManager);
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
        $configManager = (new SchemaManager($client, $coreManager, $serializer))->setCore('foo');

        $response = $configManager->flush();

        self::assertInstanceOf(CoreResponse::class, $response);
    }

    /**
     * @throws \JsonException
     */
    public function testPersist(): void
    {
        $field = new Field();
        $field->setName('foo');
        $field->setType('bar');

        $options = [
            'version' => Request::API_V2,
            'method' => Request::METHOD_POST,
            'resultclass' => QueryType::class,
            'contenttype' => 'application/json',
            'handler' => 'foo/schema',
            'rawdata' => '{"add-field":[{"name":"foo","type":"bar"}]}',
        ];

        $client = $this->getExecutingClient($options);
        $serializer = $this->getSerializer();

        $coreManager = new CoreManager($client, $serializer);
        $schemaManager = (new SchemaManager($client, $coreManager, $serializer))->setCore('foo');

        $schemaManager->addCommand(Command::ADD_FIELD, $field);

        $response = $schemaManager->persist();

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
     * @param array<string> $options
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
