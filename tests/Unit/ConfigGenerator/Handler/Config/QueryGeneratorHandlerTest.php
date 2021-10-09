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
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\QueryGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\DocumentCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\FieldValueCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\FilterCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\Query\ResultCacheVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * QueryGeneratorHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class QueryGeneratorHandlerTest extends TestCase
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

        $nodes = (new QueryGeneratorHandler($visitors))->handle($crawler, $closure);

        // just test the output of the actual visitors
        if (null === $visitors) {
            self::assertCount(7, $nodes);
            self::assertArrayHasKey('max_boolean_clauses', $nodes);
            self::assertArrayHasKey('enable_lazy_field_loading', $nodes);
            self::assertArrayHasKey('query_result_window_size', $nodes);
            self::assertArrayHasKey('query_result_max_docs_cached', $nodes);
            self::assertArrayHasKey('filter_cache', $nodes);
            self::assertArrayHasKey('query_result_cache', $nodes);
            self::assertArrayHasKey('document_cache', $nodes);
            self::assertArrayNotHasKey('use_filter_for_sorted_query', $nodes);

            self::assertSame('${solr.max.booleanClauses:1024}', $nodes['max_boolean_clauses']);
            self::assertSame('true', $nodes['enable_lazy_field_loading']);
        }
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        self::assertTrue((new QueryGeneratorHandler($this->getVisitors()))->supports(ConfigConfigurationGenerator::TYPE_QUERY));
        self::assertFalse((new QueryGeneratorHandler($this->getVisitors()))->supports(SchemaConfigurationGenerator::TYPE_COPY_FIELD));
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
            new FilterCacheVisitor(),
            new ResultCacheVisitor(),
            new DocumentCacheVisitor(),
            new FieldValueCacheVisitor(),
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getMockedVisitors(): array
    {
        $filterCache = $this->getMockBuilder(FilterCacheVisitor::class)->getMock();
        $filterCache->expects(self::atLeast(1))->method('visit');

        $queryResultCache = $this->getMockBuilder(ResultCacheVisitor::class)->getMock();
        $queryResultCache->expects(self::atLeast(1))->method('visit');

        $documentCache = $this->getMockBuilder(DocumentCacheVisitor::class)->getMock();
        $documentCache->expects(self::atLeast(1))->method('visit');

        $fieldValueCache = $this->getMockBuilder(FieldValueCacheVisitor::class)->getMock();
        $fieldValueCache->expects(self::atLeast(1))->method('visit');

        return [
            $filterCache,
            $queryResultCache,
            $documentCache,
            $fieldValueCache,
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
    <cache name="perSegFilter"
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
