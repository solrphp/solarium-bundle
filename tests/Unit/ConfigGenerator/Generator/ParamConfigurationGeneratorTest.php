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
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ParamConfigurationGenerator;
use Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Fethcher\StubFetcher;
use Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Handler\StubHandler;

/**
 * Param Configuration Generator Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamConfigurationGeneratorTest extends TestCase
{
    /**
     * test generate.
     */
    public function testGenerate(): void
    {
        $types = [
            ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
        ];

        $handlers = [
            $this->getHandlerMock(),
            $this->getHandlerMock(),
        ];

        $fetcher = $this->getFetcherMock('foo');

        $generator = new ParamConfigurationGenerator($handlers, $fetcher);
        $generator->generate('foo', $types);

        self::assertNotNull($generator->getNodes());
        self::assertArrayHasKey(ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP, $generator->getNodes());
        self::assertCount(1, $generator->getNodes());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGenerateIllegalType(): void
    {
        $types = [
            ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP,
            ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT,
        ];

        $handlers = [
            $this->getHandlerMock(),
        ];

        $fetcher = $this->getFetcherMock('foo');

        $generator = new ParamConfigurationGenerator($handlers, $fetcher);
        $generator->generate('foo', $types);

        self::assertNotContains(ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT, $generator->getTypes());
        self::assertCount(1, $generator->getTypes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNodeName(): void
    {
        self::assertSame(ParamConfigurationGenerator::$nodeName, (new ParamConfigurationGenerator([], new StubFetcher()))->getNodeName());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGetNodes(): void
    {
        self::assertNull((new ParamConfigurationGenerator([], new StubFetcher()))->getNodes());
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
        $handler->expects(self::once())
            ->method('supports')
            ->with(ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP)
            ->willReturn(true)
        ;

        $handler->expects(self::once())->method('handle');

        return $handler;
    }
}
