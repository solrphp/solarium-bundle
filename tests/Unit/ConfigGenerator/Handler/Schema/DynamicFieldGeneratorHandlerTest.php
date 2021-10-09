<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Schema;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Schema\DynamicFieldGeneratorHandler;
use Symfony\Component\DomCrawler\Crawler;

/**
 * DynamicFieldGeneratorHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DynamicFieldGeneratorHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testHandle(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getSchemaXml());

        $nodes = (new DynamicFieldGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(3, $nodes);
        self::assertArrayHasKey('name', $nodes[0]);
        self::assertSame('*_str', $nodes[0]['name']);
        self::assertSame('false', $nodes[0]['use_doc_values_as_stored']);
        self::assertArrayNotHasKey('use_doc_values_as_stored', $nodes[1]);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testBuggyCrawler(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $combine = array_combine(DynamicFieldGeneratorHandler::$attributes, \Solrphp\SolariumBundle\ConfigGenerator\Handler\Schema\DynamicFieldGeneratorHandler::$attributes);

        $crawler = $this->getMockBuilder(Crawler::class)->getMock();
        $crawler->expects(self::once())->method('filterXPath')->willReturnSelf();
        $crawler->expects(self::once())->method('extract')->willReturn(['foo' => ['bar' => 'baz'], 'qux' => $combine]);

        $nodes = (new DynamicFieldGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(1, $nodes);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        self::assertTrue((new DynamicFieldGeneratorHandler())->supports(SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD));
        self::assertFalse((new DynamicFieldGeneratorHandler())->supports(SchemaConfigurationGenerator::TYPE_FIELD_TYPE));
    }

    /**
     * @return string
     */
    private function getSchemaXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<schema name="default-config" version="1.6">
  <uniqueKey>id</uniqueKey>
  <field name="id" type="string" multiValued="false" indexed="true" required="true" stored="true"/>
  <field name="inStock" type="booleans"/>
  <field name="includes" type="text_general"/>
  <dynamicField name="*_str" type="strings" docValues="true" indexed="false" stored="false" useDocValuesAsStored="false"/>
  <dynamicField name="*_d" type="pdouble" indexed="true" stored="true"/>
  <dynamicField name="*_p" type="location" indexed="true" stored="true"/>
  <copyField source="features" dest="features_str" maxChars="256"/>
  <copyField source="name" dest="name_str" maxChars="256"/>
  <copyField source="cat" dest="cat_str" maxChars="256"/>
</schema>
XML;
    }
}
