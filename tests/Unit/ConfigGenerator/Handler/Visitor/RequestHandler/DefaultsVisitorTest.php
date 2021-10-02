<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Visitor\RequestHandler;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestHandler\DefaultsVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * DefaultsVisitorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DefaultsVisitorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \RuntimeException
     */
    public function testVisit(): void
    {
        $crawler = new Crawler($this->getXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new DefaultsVisitor())->visit($crawler, $closure, $node);

        self::assertArrayHasKey('defaults', $node);
        self::assertArrayHasKey('echo_params', $node['defaults']);
        self::assertSame('10', $node['defaults']['rows']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyNode(): void
    {
        $crawler = new Crawler($this->getEmptyXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new DefaultsVisitor())->visit($crawler, $closure, $node);

        self::assertCount(0, $node);
    }

    /**
     * @return string
     */
    private function getXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" ?>
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
