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
use Solrphp\SolariumBundle\ConfigGenerator\Handler\FieldTypeGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\CharFilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\FilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\TokenizerFieldTypeVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * FieldTypeGeneratorHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldTypeGeneratorHandlerTest extends TestCase
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
        $crawler = new Crawler($this->getSchemaXml());

        $nodes = (new FieldTypeGeneratorHandler($visitors))->handle($crawler, $closure);

        // just test the output of the actual visitors
        if (null === $visitors) {
            self::assertCount(3, $nodes);
            self::assertArrayHasKey('position_increment_gap', $nodes[0]);
            self::assertSame('100', $nodes[0]['position_increment_gap']);
            self::assertSame('solr.LowerCaseFilterFactory', $nodes[0]['analyzers'][0]['filters'][0]['class']);
            self::assertSame('solr.StandardTokenizerFactory', $nodes[0]['analyzers'][0]['tokenizer']['class']);
            self::assertSame('solr.KeywordTokenizerFactory', $nodes[1]['analyzers'][1]['tokenizer']['class']);
            self::assertSame('index', $nodes[1]['analyzers'][0]['type']);
            self::assertSame('solr.PersianCharFilterFactory', $nodes[2]['analyzers'][0]['char_filters'][0]['class']);

            self::assertArrayNotHasKey('auto_generate_phrase_queries', $nodes[0]);
            self::assertArrayNotHasKey('type', $nodes[0]['analyzers'][0]);
            self::assertArrayNotHasKey('mapping', $nodes[1]['analyzers'][0]['tokenizer']);
            self::assertArrayNotHasKey('language', $nodes[0]['analyzers'][0]['filters'][1]);
            self::assertArrayNotHasKey('mapping', $nodes[2]['analyzers'][0]['char_filters'][0]);
        }
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        self::assertTrue((new FieldTypeGeneratorHandler($this->getVisitors()))->supports(ConfigGenerator::TYPE_FIELD_TYPE));
        self::assertFalse((new FieldTypeGeneratorHandler($this->getVisitors()))->supports(ConfigGenerator::TYPE_COPY_FIELD));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testArrayCombineFailsave(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getSchemaXml());

        $nodes = (new FieldTypeGeneratorHandler($this->getVisitors()))->handle($crawler, $closure);

        self::assertArrayNotHasKey('tokenizer', $nodes[0]['analyzers']);
    }

    /**
     * @return \Generator<array<array<\Solrphp\SolariumBundle\Contract\ConfigGenerator\FieldTypeVisitorInterface>|null>>
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
     * @return string
     */
    public function getBrokedXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<schema name="default-config" version="1.6">
  <uniqueKey>id</uniqueKey>
  <fieldType name="text_sv" class="solr.TextField" positionIncrementGap="100">
    <analyzer>
      <tokenizer class="solr.StandardTokenizerFactory"/>
      <tokenizer class="solr.Foo"/>
      <filter class="solr.LowerCaseFilterFactory"/>
      <filter class="solr.StopFilterFactory" format="snowball" words="lang/stopwords_sv.txt" ignoreCase="true"/>
      <filter class="solr.SnowballPorterFilterFactory" language="Swedish"/>
    </analyzer>
  </fieldType>
  <fieldType name="descendent_path" class="solr.TextField">
    <analyzer type="index">
      <tokenizer class="solr.PathHierarchyTokenizerFactory" delimiter="/"/>
    </analyzer>
    <analyzer type="query">
      <tokenizer class="solr.KeywordTokenizerFactory"/>
    </analyzer>
  </fieldType>
  <fieldType name="text_fa" class="solr.TextField" positionIncrementGap="100">
    <analyzer>
      <charFilter class="solr.PersianCharFilterFactory"/>
      <tokenizer class="solr.StandardTokenizerFactory"/>
      <filter class="solr.LowerCaseFilterFactory"/>
      <filter class="solr.ArabicNormalizationFilterFactory"/>
      <filter class="solr.PersianNormalizationFilterFactory"/>
      <filter class="solr.StopFilterFactory" words="lang/stopwords_fa.txt" ignoreCase="true"/>
    </analyzer>
  </fieldType>
</schema>
XML;
    }

    /**
     * @return string
     */
    public function getSchemaXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<schema name="default-config" version="1.6">
  <uniqueKey>id</uniqueKey>
  <fieldType name="text_sv" class="solr.TextField" positionIncrementGap="100">
    <analyzer>
      <tokenizer class="solr.StandardTokenizerFactory"/>
      <filter class="solr.LowerCaseFilterFactory"/>
      <filter class="solr.StopFilterFactory" format="snowball" words="lang/stopwords_sv.txt" ignoreCase="true"/>
      <filter class="solr.SnowballPorterFilterFactory" language="Swedish"/>
    </analyzer>
  </fieldType>
  <fieldType name="descendent_path" class="solr.TextField">
    <analyzer type="index">
      <tokenizer class="solr.PathHierarchyTokenizerFactory" delimiter="/"/>
    </analyzer>
    <analyzer type="query">
      <tokenizer class="solr.KeywordTokenizerFactory"/>
    </analyzer>
  </fieldType>
  <fieldType name="text_fa" class="solr.TextField" positionIncrementGap="100">
    <analyzer>
      <charFilter class="solr.PersianCharFilterFactory"/>
      <tokenizer class="solr.StandardTokenizerFactory"/>
      <filter class="solr.LowerCaseFilterFactory"/>
      <filter class="solr.ArabicNormalizationFilterFactory"/>
      <filter class="solr.PersianNormalizationFilterFactory"/>
      <filter class="solr.StopFilterFactory" words="lang/stopwords_fa.txt" ignoreCase="true"/>
    </analyzer>
  </fieldType>
</schema>
XML;
    }

    /**
     * @return array<\Solrphp\SolariumBundle\Contract\ConfigGenerator\FieldTypeVisitorInterface>
     */
    private function getVisitors(): array
    {
        return [
            new CharFilterFieldTypeVisitor(),
            new FilterFieldTypeVisitor(),
            new TokenizerFieldTypeVisitor(),
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getMockedVisitors(): array
    {
        $charFilter = $this->getMockBuilder(CharFilterFieldTypeVisitor::class)->getMock();
        $charFilter->expects(self::atLeast(1))->method('visit');

        $filter = $this->getMockBuilder(FilterFieldTypeVisitor::class)->getMock();
        $filter->expects(self::atLeast(1))->method('visit');

        $token = $this->getMockBuilder(TokenizerFieldTypeVisitor::class)->getMock();
        $token->expects(self::atLeast(1))->method('visit');

        return [
            $charFilter,
            $filter,
            $token,
        ];
    }
}
