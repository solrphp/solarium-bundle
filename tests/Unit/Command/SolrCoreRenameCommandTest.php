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
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreRenameCommand;
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
class SolrCoreRenameCommandTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecuteNoCoreArgument(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "core").');

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();

        $application->add(new SolrCoreRenameCommand($manager));

        $command = $application->find('solr:core:rename');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'other' => 'foo',
        ]);

        self::assertSame(Command::INVALID, $commandTester->getStatusCode());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecuteNoOtherArgument(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "other").');

        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();

        $application->add(new SolrCoreRenameCommand($manager));

        $command = $application->find('solr:core:rename');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

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
        $manager->expects(self::once())->method('rename')->willReturn($response);

        $application->add(new SolrCoreRenameCommand($manager));

        $command = $application->find('solr:core:rename');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
            'other' => 'bar',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertSame('successfully renamed core foo to bar', trim($commandTester->getDisplay()));
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
        $manager->expects(self::once())->method('rename')->willReturn($response);

        $application->add(new SolrCoreRenameCommand($manager));

        $command = $application->find('solr:core:rename');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
            'other' => 'bar',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('error renaming core foo: '.$message.' (1)', trim($commandTester->getDisplay()));
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

        $application->add(new SolrCoreRenameCommand($manager));

        $command = $application->find('solr:core:rename');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(
            [
                'command' => $command->getName(),
            ],
            $options
        ));
    }

    /**
     * @return \Generator<array<string, array<string, bool|string|null>>>
     */
    public function provideOptions(): \Generator
    {
        yield 'core_other' => [
            'options' => [
                'core' => 'foo',
                'other' => 'bar',
            ],
            'keys' => [
                'core' => 'foo',
                'other' => 'bar',
                'action' => 'RENAME',
            ],
        ];
        yield 'async' => [
            'options' => [
                'core' => 'foo',
                'other' => 'bar',
                '--async' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'other' => 'bar',
                'async' => 'foo',
                'action' => 'RENAME',
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
