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
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreUnloadCommand;
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
class SolrCoreUnloadCommandTest extends TestCase
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

        $application->add(new SolrCoreUnloadCommand($manager));

        $command = $application->find('solr:core:unload');
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
        $manager->expects(self::once())->method('unload')->willReturn($response);

        $application->add(new SolrCoreUnloadCommand($manager));

        $command = $application->find('solr:core:unload');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertSame('successfully unloaded core foo', trim($commandTester->getDisplay()));
    }

    /**
     * @dataProvider providerError
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
        $manager->expects(self::once())->method('unload')->willReturn($response);

        $application->add(new SolrCoreUnloadCommand($manager));

        $command = $application->find('solr:core:unload');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('error unloading core foo: '.$message.' (1)', trim($commandTester->getDisplay()));
    }

    /**
     * @dataProvider provideOptions
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

        $application->add(new SolrCoreUnloadCommand($manager));

        $command = $application->find('solr:core:unload');
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
    public function provideOptions(): \Generator
    {
        yield 'delete_index' => [
            'options' => [
                '--delete-index' => null,
            ],
            'keys' => [
                'core' => 'foo',
                'deleteIndex' => 'true',
                'action' => 'UNLOAD',
            ],
        ];

        yield 'delete_data_dir' => [
            'options' => [
                '--delete-data-dir' => null,
            ],
            'keys' => [
                'core' => 'foo',
                'deleteDataDir' => 'true',
                'action' => 'UNLOAD',
            ],
        ];

        yield 'delete_instance_dir' => [
            'options' => [
                '--delete-instance-dir' => null,
            ],
            'keys' => [
                'core' => 'foo',
                'deleteInstanceDir' => 'true',
                'action' => 'UNLOAD',
            ],
        ];

        yield 'async' => [
            'options' => [
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'async' => 'foo',
                'action' => 'UNLOAD',
            ],
        ];

        yield 'all_options' => [
            'options' => [
                '--delete-index' => null,
                '--delete-data-dir' => null,
                '--delete-instance-dir' => null,
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'deleteIndex' => 'true',
                'deleteDataDir' => 'true',
                'deleteInstanceDir' => 'true',
                'async' => 'foo',
                'action' => 'UNLOAD',
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function providerError(): \Generator
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
