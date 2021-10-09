<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Generator;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ConfigConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Fethcher\StubFetcher;
use Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Handler\StubHandler;

/**
 * ConfigConfigurationGeneratorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigConfigurationGeneratorTest extends TestCase
{
    /**
     * test generate.
     */
    public function testGenerate(): void
    {
        $types = [
            ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
            ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
        ];

        $handlers = [
            $this->getHandlerMock(),
            $this->getHandlerMock(),
        ];

        $fetcher = $this->getFetcherMock('foo');

        $generator = new ConfigConfigurationGenerator($handlers, $fetcher);
        $generator->generate('foo', $types);

        self::assertNotNull($generator->getNodes());
        self::assertArrayHasKey(ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT, $generator->getNodes());
        self::assertCount(1, $generator->getNodes());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGenerateIllegalType(): void
    {
        $types = [
            ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER,
            ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
            SchemaConfigurationGenerator::TYPE_COPY_FIELD,
        ];

        $handlers = [
            $this->getHandlerMock(),
        ];

        $fetcher = $this->getFetcherMock('foo');

        $generator = new ConfigConfigurationGenerator($handlers, $fetcher);
        $generator->generate('foo', $types);

        self::assertNotContains(SchemaConfigurationGenerator::TYPE_COPY_FIELD, $generator->getTypes());
        self::assertCount(2, $generator->getTypes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNodeName(): void
    {
        self::assertSame(ConfigConfigurationGenerator::$nodeName, (new ConfigConfigurationGenerator([], new StubFetcher()))->getNodeName());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGetNodes(): void
    {
        self::assertNull((new ConfigConfigurationGenerator([], new StubFetcher()))->getNodes());
    }

    /**
     * @param string $core
     *
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Fethcher\StubFetcher
     */
    private function getFetcherMock(string $core)
    {
        $fetcher = $this->getMockBuilder(StubFetcher::class)->getMock();
        $fetcher->expects(self::once())->method('fetchXml')->with($core);

        return $fetcher;
    }

    /**
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject|\Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Handler\StubHandler
     *
     * @throws \PHPUnit\Framework\Exception
     */
    private function getHandlerMock()
    {
        $handler = $this->getMockBuilder(StubHandler::class)->getMock();
        $handler->expects(self::exactly(2))
            ->method('supports')
            ->withConsecutive(
                [ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER],
                [ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT],
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true
            )
        ;

        $handler->expects(self::once())->method('handle');

        return $handler;
    }
}
