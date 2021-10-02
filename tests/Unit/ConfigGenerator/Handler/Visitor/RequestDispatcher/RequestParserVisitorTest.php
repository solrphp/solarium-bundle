<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Visitor\RequestDispatcher;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestDispatcher\RequestParserVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * RequestParserVisitorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestParserVisitorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testVisit(): void
    {
        $crawler = new Crawler($this->getXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new RequestParserVisitor())->visit($crawler, $closure, $node);

        self::assertArrayHasKey('request_parsers', $node);
        self::assertArrayHasKey('enable_remote_streaming', $node['request_parsers'][0]);
        self::assertArrayNotHasKey('enable_stream_body', $node['request_parsers'][0]);
        self::assertSame('-1', $node['request_parsers'][0]['formdata_upload_limit_in_kb']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testBuggyCrawler(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $combine = array_combine(RequestParserVisitor::$attributes, RequestParserVisitor::$attributes);

        $crawler = $this->getMockBuilder(Crawler::class)->getMock();
        $crawler->expects(self::once())->method('filterXPath')->willReturnSelf();
        $crawler->expects(self::once())->method('extract')->willReturn(['foo' => ['bar' => 'baz'], 'qux' => $combine]);
        $nodes = [];
        (new RequestParserVisitor())->visit($crawler, $closure, $nodes);

        self::assertCount(1, $nodes);
    }

    /**
     * @return string
     */
    private function getXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" ?>
<config>
  <requestDispatcher>
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
