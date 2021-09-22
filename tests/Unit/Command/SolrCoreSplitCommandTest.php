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
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreSplitCommand;
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
class SolrCoreSplitCommandTest extends TestCase
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

        $application->add(new SolrCoreSplitCommand($manager));

        $command = $application->find('solr:core:split');
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
        $header->setStatusCode(0);
        $response->setHeader($header);

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())->method('split')->willReturn($response);

        $application->add(new SolrCoreSplitCommand($manager));

        $command = $application->find('solr:core:split');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertSame('successfully split core foo', trim($commandTester->getDisplay()));
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

        $header->setStatusCode(1);
        $response->setHeader($header);
        $response->setError($error);

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())->method('split')->willReturn($response);

        $application->add(new SolrCoreSplitCommand($manager));

        $command = $application->find('solr:core:split');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('error splitting core foo: '.$message.' (1)', trim($commandTester->getDisplay()));
    }

    /**
     * @dataProvider provideConflictingOptions
     *
     * @param array<string, string> $options
     * @param string                $message
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testInitialize(array $options, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();

        $application->add(new SolrCoreSplitCommand($manager));

        $command = $application->find('solr:core:split');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                [
                    'command' => $command->getName(),
                    'core' => 'foo',
                ],
                $options
            )
        );

        self::assertSame(Command::INVALID, $commandTester->getStatusCode());
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
            ->getMock();

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
            ->willReturn(new Response(''));

        $response = new CoreResponse();
        $header = new Header();

        $header->setStatusCode(0);
        $response->setHeader($header);

        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializer->expects(self::once())->method('deserialize')->willReturn($response);

        $manager = new CoreManager($client, $serializer);

        $application->add(new SolrCoreSplitCommand($manager));

        $command = $application->find('solr:core:split');
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
        yield 'path' => [
            'options' => [
                'core' => 'foo',
                '--path' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'path' => 'foo',
                'action' => 'SPLIT',
            ],
        ];

        yield 'target_core' => [
            'options' => [
                'core' => 'foo',
                '--target-core' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'targetCore' => 'foo',
                'action' => 'SPLIT',
            ],
        ];

        yield 'ranges' => [
            'options' => [
                'core' => 'foo',
                '--ranges' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'ranges' => 'foo',
                'action' => 'SPLIT',
            ],
        ];

        yield 'split_key' => [
            'options' => [
                'core' => 'foo',
                '--split-key' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'split.key' => 'foo',
                'action' => 'SPLIT',
            ],
        ];

        yield 'async' => [
            'options' => [
                'core' => 'foo',
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'async' => 'foo',
                'action' => 'SPLIT',
            ],
        ];

        yield 'all_options' => [
            'options' => [
                'core' => 'foo',
                '--path' => 'foo',
                '--ranges' => 'foo',
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'path' => 'foo',
                'ranges' => 'foo',
                'async' => 'foo',
                'action' => 'SPLIT',
            ],
        ];

        yield 'all_options_two' => [
            'options' => [
                'core' => 'foo',
                '--target-core' => 'foo',
                '--split-key' => 'foo',
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'targetCore' => 'foo',
                'split.key' => 'foo',
                'async' => 'foo',
                'action' => 'SPLIT',
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

    /**
     * @return \Generator
     */
    public function provideConflictingOptions(): \Generator
    {
        yield 'path_target-core_conflict' => [
            'options' => [
                '--path' => 'foo',
                '--target-core' => 'bar',
            ],
            'message' => 'either "path" or "target-core" can be defined, not both',
        ];

        yield 'ranges_split-key_conflict' => [
            'options' => [
                '--ranges' => 'foo',
                '--split-key' => 'bar',
            ],
            'message' => 'either "ranges" or "split-key" can be defined, not both',
        ];
    }
}
