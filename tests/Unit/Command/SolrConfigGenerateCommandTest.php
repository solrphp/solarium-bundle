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
use Solrphp\SolariumBundle\Command\ConfigGenerator\SolrphpConfigGenerateCommand;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\DumperInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ConfigConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ParamConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * SolrConfigGenerateCommandTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrConfigGenerateCommandTest extends TestCase
{
    /**
     * @dataProvider provideArguments
     *
     * @param array<string> $arguments
     * @param string        $message
     */
    public function testExceptionInvalidArguments(array $arguments, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        $application = new Application();

        $generator = $this->getMockBuilder(ConfigGenerator::class)->disableOriginalConstructor()->getMock();

        $application->add(new SolrphpConfigGenerateCommand($generator));

        $command = $application->find('solr:config:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge([
            'command' => $command->getName(),
        ], $arguments));

        self::assertSame(Command::INVALID, $commandTester->getStatusCode());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testExecute(): void
    {
        $application = new Application();

        $generator = $this->getMockBuilder(ConfigGenerator::class)->disableOriginalConstructor()->getMock();
        $generator->expects(self::once())
            ->method('withCore')
            ->with('foo')
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('withExtension')
            ->with('bar')
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('withTypes')
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('generate')
        ;

        $application->add(new SolrphpConfigGenerateCommand($generator));

        $command = $application->find('solr:config:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
            'filetype' => 'bar',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertSame('successfully generated config for foo core', trim($commandTester->getDisplay()));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testGenerateException(): void
    {
        $application = new Application();

        $generator = $this->getMockBuilder(ConfigGenerator::class)->disableOriginalConstructor()->getMock();
        $generator->expects(self::once())
            ->method('withCore')
            ->with('foo')
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('withExtension')
            ->with('bar')
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('withTypes')
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('generate')
            ->willThrowException(new GeneratorException('foo'))
        ;

        $application->add(new SolrphpConfigGenerateCommand($generator));

        $command = $application->find('solr:config:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'core' => 'foo',
            'filetype' => 'bar',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('error generating config for foo core', trim($commandTester->getDisplay()));
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

        $generator = $this->getMockBuilder(ConfigGenerator::class)->disableOriginalConstructor()->getMock();
        $generator->expects(self::once())
            ->method('withCore')
            ->with('demo')
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('withExtension')
            ->with(DumperInterface::EXTENSION_YAML)
            ->willReturnSelf()
        ;

        $generator->expects(self::once())
            ->method('withTypes')
            ->with($keys)
            ->willReturnSelf()
        ;

        $application->add(new SolrphpConfigGenerateCommand($generator));

        $command = $application->find('solr:config:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(
            [
                'command' => $command->getName(),
            ],
            $options
        ));
    }

    /**
     * @return \Generator<array<string, array<int|string, string>>>
     */
    public function provideOptions(): \Generator
    {
        yield 'no_exclusion' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_fields' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-fields' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_copy_fields' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-copy-fields' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_dynamic_field' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-dynamic-fields' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_field_types' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-field-types' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_update_handler' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-update-handler' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_query' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-query' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_request_dispatcher' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-request-dispatcher' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_request_handler' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-request-handlers' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_search_component' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-search-components' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ],
        ];

        yield 'no_parameter_set_map' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-parameters' => null,
            ],
            'keys' => [
                SchemaConfigurationGenerator::TYPE_FIELD,
                SchemaConfigurationGenerator::TYPE_COPY_FIELD,
                SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD,
                SchemaConfigurationGenerator::TYPE_FIELD_TYPE,
                ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER,
                ConfigConfigurationGenerator::TYPE_QUERY,
                ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER,
                ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
                ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
            ],
        ];

        yield 'exclude_all' => [
            'options' => [
                'core' => 'demo',
                'filetype' => DumperInterface::EXTENSION_YAML,
                '--exclude-fields' => null,
                '--exclude-copy-fields' => null,
                '--exclude-dynamic-fields' => null,
                '--exclude-field-types' => null,
                '--exclude-update-handler' => null,
                '--exclude-query' => null,
                '--exclude-request-dispatcher' => null,
                '--exclude-request-handlers' => null,
                '--exclude-search-components' => null,
                '--exclude-parameters' => null,
            ],
            'keys' => [],
        ];
    }

    /**
     * @return \Generator<string|array<string, string>>
     */
    public function provideArguments(): \Generator
    {
        yield 'no_arguments' => [
            [],
            'Not enough arguments (missing: "core, filetype").',
        ];

        yield 'core_only' => [
            ['core' => 'foo'],
            'Not enough arguments (missing: "filetype").',
        ];

        yield 'filetype_only' => [
            ['filetype' => 'foo'],
            'Not enough arguments (missing: "core").',
        ];
    }
}
