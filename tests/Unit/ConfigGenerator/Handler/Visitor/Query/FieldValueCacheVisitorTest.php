<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Visitor\Query;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\Query\FieldValueCacheVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * FieldValueCacheVisitorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldValueCacheVisitorTest extends TestCase
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

        (new FieldValueCacheVisitor())->visit($crawler, $closure, $node);

        self::assertArrayHasKey('field_value_cache', $node);
        self::assertArrayHasKey('autowarm_count', $node['field_value_cache']);
        self::assertArrayNotHasKey('class', $node['field_value_cache']);
        self::assertSame('perSegFilter', $node['field_value_cache']['name']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testBuggyCrawler(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $combine = array_combine(FieldValueCacheVisitor::$attributes, FieldValueCacheVisitor::$attributes);

        $crawler = $this->getMockBuilder(Crawler::class)->getMock();
        $crawler->expects(self::once())->method('filterXPath')->willReturnSelf();
        $crawler->expects(self::once())->method('extract')->willReturn(['foo' => ['bar' => 'baz'], 'qux' => $combine]);
        $nodes = [];
        (new FieldValueCacheVisitor())->visit($crawler, $closure, $nodes);

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
  <query>
    <maxBooleanClauses>${solr.max.booleanClauses:1024}</maxBooleanClauses>
    <filterCache size="512"
                 initialSize="512"
                 autowarmCount="0"/>
    <queryResultCache size="512"
                      initialSize="512"
                      autowarmCount="0"/>
    <documentCache size="512"
                   initialSize="512"
                   autowarmCount="0"/>
    <fieldValueCache name="perSegFilter"
           size="10"
           initialSize="0"
           autowarmCount="10"
           regenerator="solr.NoOpRegenerator" />
    <enableLazyFieldLoading>true</enableLazyFieldLoading>
    <queryResultWindowSize>20</queryResultWindowSize>
    <queryResultMaxDocsCached>200</queryResultMaxDocsCached>
    <listener event="newSearcher" class="solr.QuerySenderListener">
      <arr name="queries">
      </arr>
    </listener>
    <listener event="firstSearcher" class="solr.QuerySenderListener">
      <arr name="queries">
      </arr>
    </listener>
    <useColdSearcher>false</useColdSearcher>
  </query>
</config>
XML;
    }
}
