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
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreStatusCommand;
use Solrphp\SolariumBundle\Common\Response\Error;
use Solrphp\SolariumBundle\Common\Response\Header;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\IndexTableCreator;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\StatusTableCreator;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\UserDataTableCreator;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Index;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\UserData;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Solr Core Status Command Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreStatusCommandTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecute(): void
    {
        $response = $this->getStatusResponse();
        $application = new Application();

        $manager = $this->getMockBuilder(CoreManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())->method('status')->willReturn($response);

        $statusTable = $this->getMockBuilder(StatusTableCreator::class)->getMock();
        $statusTable->expects(self::once())->method('create')->with(self::anything(), $response);

        $indexTable = $this->getMockBuilder(IndexTableCreator::class)->getMock();
        $indexTable->expects(self::once())->method('create')->with(self::anything(), $response);

        $userDataTable = $this->getMockBuilder(UserDataTableCreator::class)->getMock();
        $userDataTable->expects(self::once())->method('create')->with(self::anything(), $response);

        $application->add(new \Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreStatusCommand($manager, $statusTable, $indexTable, $userDataTable));

        $command = $application->find('solr:core:status');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @dataProvider providerError
     *
     * @param \Solrphp\SolariumBundle\Common\Response\Error|null $error
     * @param string                                             $message
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
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
        $manager->expects(self::once())->method('status')->willReturn($response);

        $application->add(new SolrCoreStatusCommand($manager));

        $command = $application->find('solr:core:status');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('error while retrieving status: '.$message.' (1)', trim($commandTester->getDisplay()));
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

        $client = $this->getMockBuilder(\Solarium\Client::class)
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

        $application->add(new SolrCoreStatusCommand($manager));

        $command = $application->find('solr:core:status');
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
        yield 'core_status' => [
            'options' => [
                '--core' => 'foo',
            ],
            'keys' => [
                'core' => 'foo',
                'action' => 'STATUS',
            ],
        ];

        yield 'omit_index' => [
            'options' => [
                '--omit-index-info' => null,
            ],
            'keys' => [
                'indexInfo' => 'false',
                'action' => 'STATUS',
            ],
        ];

        yield 'all_options' => [
            'options' => [
                '--core' => 'foo',
                '--omit-index-info' => null,
            ],
            'keys' => [
                'core' => 'foo',
                'indexInfo' => 'false',
                'action' => 'STATUS',
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
     * @return \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse
     */
    private function getStatusResponse(): StatusResponse
    {
        $userData = new UserData();
        $userData->setCommitCommandVer('foo');
        $userData->setCommitTimeMSec('bar');

        $index = new Index();
        $index->setCurrent(true);
        $index->setDeletedDocs(2);
        $index->setDirectory('foo');
        $index->setHasDeletions(true);
        $index->setIndexHeapUsageBytes(10);
        $index->setLastModified(date_create('1970-01-01 00:00:00'));
        $index->setMaxDoc(10);
        $index->setNumDocs(3);
        $index->setSegmentCount(8);
        $index->setSegmentsFile('bar');
        $index->setSize('9');
        $index->setSizeInBytes(90);
        $index->setVersion(8);
        $index->setUserData($userData);

        $status = new Status();
        $status->setName('foo');
        $status->setConfig('bar');
        $status->setDataDir('baz');
        $status->setInstanceDir('qux');
        $status->setSchema('quux');
        $status->setStartTime(date_create('1970-01-01 00:00:00'));
        $status->setUptime(3);

        $status->setIndex($index);

        $header = new Header();
        $header->setStatusCode(0);
        $response = new StatusResponse();
        $response->addStatus($status);
        $response->setHeader($header);

        return $response;
    }
}
