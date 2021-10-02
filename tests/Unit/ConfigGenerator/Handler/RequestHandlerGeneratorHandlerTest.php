<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\RequestHandlerGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestDispatcher\RequestParserVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * RequestHandlerGeneratorHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestHandlerGeneratorHandlerTest extends TestCase
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

        $nodes = (new RequestHandlerGeneratorHandler($visitors))->handle($crawler, $closure);

        // just test the output of the actual visitors
        if (null === $visitors) {
            self::assertCount(2, $nodes);
            self::assertArrayHasKey('name', $nodes[0]);
            self::assertArrayHasKey('class', $nodes[0]);
            self::assertArrayHasKey('appends', $nodes[0]);
            self::assertArrayHasKey('invariants', $nodes[0]);
            self::assertArrayHasKey('components', $nodes[0]);
            self::assertArrayHasKey('defaults', $nodes[1]);

            self::assertSame('solr.SearchHandler', $nodes[0]['class']);
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

        (new RequestHandlerGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(0, $node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        self::assertTrue((new RequestHandlerGeneratorHandler($this->getVisitors()))->supports(ConfigGenerator::TYPE_REQUEST_HANDLER));
        self::assertFalse((new RequestHandlerGeneratorHandler($this->getVisitors()))->supports(ConfigGenerator::TYPE_COPY_FIELD));
    }

    /**
     * @return \Generator<array<array<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>|null>>
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
     * @return array<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>
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
  <requestHandler name="/select" class="solr.SearchHandler">
   <lst name="defaults">
      <str name="echoParams">explicit</str>
      <int name="rows">10</int>
    </lst>
       <lst name="appends">
         <str name="fq">inStock:true</str>
       </lst>
       <lst name="invariants">
         <str name="facet.field">cat</str>
         <str name="facet.field">manu_exact</str>
         <str name="facet.query">price:[* TO 500]</str>
         <str name="facet.query">price:[500 TO *]</str>
       </lst>
       <arr name="components">
         <str>nameOfCustomComponent1</str>
         <str>nameOfCustomComponent2</str>
       </arr>
  </requestHandler>
  <requestHandler name="/query" class="solr.SearchHandler">
    <lst name="defaults">
      <str name="echoParams">explicit</str>
      <str name="wt">json</str>
      <str name="indent">true</str>
    </lst>
  </requestHandler>
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
<config>
  <requestHandler name="/select" class="solr.SearchHandler">
  </requestHandler>
</config>
XML;
    }
}
