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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Command\Config\SolrConfigUpdateCommand;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Solr ConfigUpdate Command Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrConfigUpdateCommandTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecuteNoArgument(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "core").');

        $application = new Application();

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $processor = new ConfigProcessor(new ArrayCollection(), $manager);
        $store = new SolrConfigurationStore([], [], new SolrSerializer());

        $application->add(new SolrConfigUpdateCommand($processor, $store));

        $command = $application->find('solr:config:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        self::assertSame(Command::INVALID, $commandTester->getStatusCode());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecute(): void
    {
        $store = new SolrConfigurationStore([$this->getEmptySchemaConfig()], [$this->getEmptyConfigConfig()], new SolrSerializer());

        $application = new Application();

        $processor = $this->getMockBuilder(ConfigProcessor::class)->disableOriginalConstructor()->getMock();
        $processor->expects(self::once())->method('withCore')->with('foo')->willReturnSelf();
        $processor->expects(self::once())->method('withConfig')->willReturnSelf();
        $processor->expects(self::once())->method('process');

        $application->add(new SolrConfigUpdateCommand($processor, $store));

        $command = $application->find('solr:config:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertSame('Successfully updated config for foo core', trim($commandTester->getDisplay()));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecuteFailure(): void
    {
        $store = new SolrConfigurationStore([$this->getEmptySchemaConfig()], [$this->getEmptyConfigConfig()], new SolrSerializer());

        $application = new Application();
        $exception = new ProcessorException('error message');

        $processor = $this->getMockBuilder(ConfigProcessor::class)->disableOriginalConstructor()->getMock();
        $processor->expects(self::once())->method('withCore')->with('foo')->willReturnSelf();
        $processor->expects(self::once())->method('withConfig')->willReturnSelf();
        $processor->expects(self::once())->method('process')->willThrowException($exception);

        $application->add(new SolrConfigUpdateCommand($processor, $store));

        $command = $application->find('solr:config:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('Unable to process config for foo core: error message', trim($commandTester->getDisplay()));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExecuteNoConfig(): void
    {
        $store = new SolrConfigurationStore([$this->getEmptySchemaConfig()], [$this->getEmptyConfigConfig()], new SolrSerializer());

        $application = new Application();

        $processor = $this->getMockBuilder(ConfigProcessor::class)->disableOriginalConstructor()->getMock();
        $processor->expects(self::never())->method('withCore');
        $processor->expects(self::never())->method('withConfig');
        $processor->expects(self::never())->method('process');

        $application->add(new SolrConfigUpdateCommand($processor, $store));

        $command = $application->find('solr:config:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'bar',
        ]);

        self::assertSame(Command::INVALID, $commandTester->getStatusCode());
        self::assertSame('No config found for bar core', trim($commandTester->getDisplay()));
    }

    /**
     * @return array<string, mixed>
     */
    private function getEmptySchemaConfig(): array
    {
        return [
            'cores' => ['foo'],
            'fields' => [],
            'dynamic_fields' => [],
            'copy_fields' => [],
            'field_types' => [],
            'unique_key' => 'foo',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getEmptyConfigConfig(): array
    {
        return [
            'cores' => ['foo'],
            'search_components' => [],
            'request_handlers' => [],
        ];
    }
}
