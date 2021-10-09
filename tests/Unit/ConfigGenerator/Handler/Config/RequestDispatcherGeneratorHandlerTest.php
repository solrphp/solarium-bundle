<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Config;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ConfigConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\RequestDispatcherGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestDispatcher\RequestParserVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * RequestDispatcherGeneratorHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestDispatcherGeneratorHandlerTest extends TestCase
{
    /**
     * @dataProvider provideVisitors
     *
     * @param array|null $visitors
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testHandle(?array $visitors): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getXml());

        $nodes = (new RequestDispatcherGeneratorHandler($visitors))->handle($crawler, $closure);

        // just test the output of the actual visitors
        if (null === $visitors) {
            self::assertCount(2, $nodes);
            self::assertArrayHasKey('handle_select', $nodes);
            self::assertArrayHasKey('request_parsers', $nodes);
            self::assertArrayNotHasKey('http_caching', $nodes);
            self::assertIsArray($nodes['request_parsers']);

            self::assertSame('true', $nodes['handle_select']);
        }
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyNode(): void
    {
        $crawler = new Crawler($this->getEmptyXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new RequestDispatcherGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(0, $node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        self::assertTrue((new RequestDispatcherGeneratorHandler($this->getVisitors()))->supports(ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER));
        self::assertFalse((new RequestDispatcherGeneratorHandler($this->getVisitors()))->supports(SchemaConfigurationGenerator::TYPE_COPY_FIELD));
    }

    /**
     * @return \Generator<array<array<\Solrphp\SolariumBundle\ConfigGenerator\Contract\FieldTypeVisitorInterface>|null>>
     */
    public function provideVisitors(): \Generator
    {
        yield 'provided_visitors' => [
            $this->getMockedVisitors(),
        ];

        yield 'default_visitors' => [
            null,
        ];
    }

    /**
     * @return array<\Solrphp\SolariumBundle\ConfigGenerator\Contract\FieldTypeVisitorInterface>
     */
    private function getVisitors(): array
    {
        return [
            new RequestParserVisitor(),
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getMockedVisitors(): array
    {
        $requestParser = $this->getMockBuilder(RequestParserVisitor::class)->getMock();
        $requestParser->expects(self::atLeast(1))->method('visit');

        return [
            $requestParser,
        ];
    }

    /**
     * @return string
     */
    private function getXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" ?>
<config>
  <requestDispatcher handleSelect="true">
    <requestParsers enableRemoteStreaming="false"
                    multipartUploadLimitInKB="-1"
                    formdataUploadLimitInKB="-1"
                    addHttpRequestToContext="false"/>
    <httpCaching never304="true" />
  </requestDispatcher>
</config>
XML;
    }

    /**
     * @return string
     */
    private function getEmptyXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" ?>
  <requestHandler name="/select" class="solr.SearchHandler">
  </requestHandler>
XML;
    }
}
