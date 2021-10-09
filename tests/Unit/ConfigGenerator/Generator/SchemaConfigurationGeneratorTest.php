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
 * Schema ConfigurationGenerator Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaConfigurationGeneratorTest extends TestCase
{
    /**
     * test generate.
     */
    public function testGenerate(): void
    {
        $types = [
            SchemaConfigurationGenerator::TYPE_FIELD,
            SchemaConfigurationGenerator::TYPE_COPY_FIELD,
        ];

        $handlers = [
            $this->getHandlerMock(),
            $this->getHandlerMock(),
        ];

        $fetcher = $this->getFetcherMock('foo');

        $generator = new SchemaConfigurationGenerator($handlers, $fetcher);
        $generator->generate('foo', $types);

        self::assertNotNull($generator->getNodes());
        self::assertArrayHasKey(SchemaConfigurationGenerator::TYPE_COPY_FIELD, $generator->getNodes());
        self::assertCount(1, $generator->getNodes());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGenerateIllegalType(): void
    {
        $types = [
            SchemaConfigurationGenerator::TYPE_FIELD,
            ConfigConfigurationGenerator::TYPE_QUERY,
            SchemaConfigurationGenerator::TYPE_COPY_FIELD,
        ];

        $handlers = [
            $this->getHandlerMock(),
        ];

        $fetcher = $this->getFetcherMock('foo');

        $generator = new SchemaConfigurationGenerator($handlers, $fetcher);
        $generator->generate('foo', $types);

        self::assertNotContains(ConfigConfigurationGenerator::TYPE_QUERY, $generator->getTypes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNodeName(): void
    {
        self::assertSame(SchemaConfigurationGenerator::$nodeName, (new SchemaConfigurationGenerator([], new StubFetcher()))->getNodeName());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGetNodes(): void
    {
        self::assertNull((new SchemaConfigurationGenerator([], new StubFetcher()))->getNodes());
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
                [SchemaConfigurationGenerator::TYPE_FIELD],
                [SchemaConfigurationGenerator::TYPE_COPY_FIELD],
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
