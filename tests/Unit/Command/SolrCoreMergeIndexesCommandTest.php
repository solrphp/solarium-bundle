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
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreMergeIndexesCommand;
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
class SolrCoreMergeIndexesCommandTest extends TestCase
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

        $application->add(new SolrCoreMergeIndexesCommand($manager));

        $command = $application->find('solr:core:merge-indexes');
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
        $manager->expects(self::once())->method('mergeIndexes')->willReturn($response);

        $application->add(new SolrCoreMergeIndexesCommand($manager));

        $command = $application->find('solr:core:merge-indexes');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
            '--index-dir' => 'bar',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertSame('successfully merged indexes for core foo', trim($commandTester->getDisplay()));
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
        $manager->expects(self::once())->method('mergeIndexes')->willReturn($response);

        $application->add(new SolrCoreMergeIndexesCommand($manager));

        $command = $application->find('solr:core:merge-indexes');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
            '--index-dir' => 'bar',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('error merging indexes for core foo: '.$message.' (1)', trim($commandTester->getDisplay()));
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

        $header->setStatusCode(0);
        $response->setHeader($header);

        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializer->expects(self::once())->method('deserialize')->willReturn($response);

        $manager = new CoreManager($client, $serializer);

        $application->add(new SolrCoreMergeIndexesCommand($manager));

        $command = $application->find('solr:core:merge-indexes');
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
        yield 'index_dir' => [
            'options' => [
                'core' => 'foo',
                '--index-dir' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'indexDir' => 'foo',
                'action' => 'MERGEINDEXES',
            ],
        ];

        yield 'src_core' => [
            'options' => [
                'core' => 'foo',
                '--src-core' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'srcCore' => 'foo',
                'action' => 'MERGEINDEXES',
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
                'action' => 'MERGEINDEXES',
            ],
        ];

        yield 'all_options' => [
            'options' => [
                'core' => 'foo',
                '--index-dir' => 'foo',
                '--src-core' => 'foo',
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'indexDir' => 'foo',
                'srcCore' => 'foo',
                'async' => 'foo',
                'action' => 'MERGEINDEXES',
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
