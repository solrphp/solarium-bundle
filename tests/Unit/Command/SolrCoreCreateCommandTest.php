<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreCreateCommand;
use Solrphp\SolariumBundle\Common\Response\Error;
use Solrphp\SolariumBundle\Common\Response\Header;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Solr Core Reload Command Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreCreateCommandTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecuteNoArgument(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "core").');

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();

        $application->add(new SolrCoreCreateCommand($manager));

        $command = $application->find('solr:core:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        self::assertSame(Command::INVALID, $commandTester->getStatusCode());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testExecute(): void
    {
        $response = new CoreResponse();
        $header = new Header();
        $header->setStatus(0);
        $response->setResponseHeader($header);

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())->method('create')->willReturn($response);

        $application->add(new SolrCoreCreateCommand($manager));

        $command = $application->find('solr:core:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertSame('successfully created core foo', trim($commandTester->getDisplay()));
    }

    /**
     * @dataProvider errorProvider
     *
     * @param \Solrphp\SolariumBundle\Common\Response\Error|null $error
     * @param string                                             $message
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function testExecutionFail(?Error $error, string $message): void
    {
        $response = new CoreResponse();
        $header = new Header();

        $header->setStatus(1);
        $response->setResponseHeader($header);
        $response->setError($error);

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())->method('create')->willReturn($response);

        $application->add(new SolrCoreCreateCommand($manager));

        $command = $application->find('solr:core:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('error creating core foo: '.$message.' (1)', trim($commandTester->getDisplay()));
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param array<string, string|null> $options
     * @param array<string, bool|string> $keys
     */
    public function testExecutionOptions(array $options, array $keys): void
    {
        $application = new Application();

        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([new Curl(), new EventDispatcher()])
            ->onlyMethods(['executeRequest'])
            ->getMock()
        ;

        $client->expects(self::once())
            ->method('executeRequest')
            ->with(
                self::callback(static function ($request) use ($keys) {
                    return $keys === $request->getParams();
                }),
                self::callback(static function ($endpoint) {
                    return 'admin' === $endpoint->getCollection();
                })
            )
            ->willReturn(new Response(''))
        ;

        $response = new CoreResponse();
        $header = new Header();

        $header->setStatus(0);
        $response->setResponseHeader($header);

        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializer->expects(self::once())->method('deserialize')->willReturn($response);

        $manager = new CoreManager($client, $serializer);

        $application->add(new SolrCoreCreateCommand($manager));

        $command = $application->find('solr:core:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(
            [
                'command' => $command->getName(),
                'core' => 'foo',
            ],
            $options
        ));
    }

    /**
     * @return \Generator<array<string, array<string, bool|string|null>>>
     */
    public function optionsProvider(): \Generator
    {
        yield 'instance_dir' => [
            'options' => [
                '--instance-dir' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'instanceDir' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'config' => [
            'options' => [
                '--config' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'config' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'schema' => [
            'options' => [
                '--schema' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'schema' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'data_dir' => [
            'options' => [
                '--data-dir' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'dataDir' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'config_set' => [
            'options' => [
                '--config-set' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'configSet' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'collection' => [
            'options' => [
                '--collection' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'collection' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'shard' => [
            'options' => [
                '--shard' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'shard' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'async' => [
            'options' => [
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'async' => 'foo',
                'action' => 'CREATE',
            ],
        ];

        yield 'all_configs' => [
            'options' => [
                '--instance-dir' => 'foo',
                '--config' => 'foo',
                '--schema' => 'foo',
                '--data-dir' => 'foo',
                '--config-set' => 'foo',
                '--collection' => 'foo',
                '--shard' => 'foo',
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'instanceDir' => 'foo',
                'config' => 'foo',
                'schema' => 'foo',
                'dataDir' => 'foo',
                'configSet' => 'foo',
                'collection' => 'foo',
                'shard' => 'foo',
                'async' => 'foo',
                'action' => 'CREATE',
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function errorProvider(): \Generator
    {
        $error = new Error();
        $error->setMessage($message = 'lorem ipsum');

        yield 'error_response' => [
            'error' => $error,
            'message' => $message,
        ];

        yield 'null_error' => [
            'error' => null,
            'message' => '[unable to get error message]',
        ];
    }
}
