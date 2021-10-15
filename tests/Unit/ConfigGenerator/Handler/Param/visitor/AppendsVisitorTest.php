<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Param\visitor;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor\AppendsVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Appends Visitor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AppendsVisitorTest extends TestCase
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

        (new AppendsVisitor())->visit($crawler, $closure, $node);

        self::assertArrayHasKey('_appends_', $node);
        self::assertCount(2, $node['_appends_']);
        self::assertArrayHasKey('name', $node['_appends_'][1]);
        self::assertArrayHasKey('value', $node['_appends_'][1]);
        self::assertArrayNotHasKey('foo', $node['_appends_'][0]);
        self::assertSame('facet', $node['_appends_'][0]['name']);
        self::assertSame('true', $node['_appends_'][0]['value']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyNode(): void
    {
        $crawler = new Crawler($this->getEmptyXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new AppendsVisitor())->visit($crawler, $closure, $node);

        self::assertCount(0, $node);
    }

    /**
     * @return string
     */
    private function getXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<lst name="henkelmans">
      <str name="facet">true</str>
      <long name="facet.limit">5</long>
      <lst name="_invariants_">
        <bool name="facet">true</bool>
        <str name="foo">bar</str>
      </lst>
      <lst name="_appends_">
        <bool name="facet">true</bool>
        <str name="foo">bar</str>
      </lst>
      <lst name="foo">
        <long name="v">3</long>
      </lst>
    </lst>
XML;
    }

    /**
     * @return string
     */
    private function getEmptyXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>

<lst name="responseHeader">
  <int name="status">0</int>
  <int name="QTime">0</int>
</lst>
<lst name="response" />
</response>
XML;
    }
}
