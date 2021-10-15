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
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor\InvariantsVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Invariants Visitor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class InvariantsVisitorTest extends TestCase
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

        (new InvariantsVisitor())->visit($crawler, $closure, $node);

        self::assertArrayHasKey('_invariants_', $node);
        self::assertCount(2, $node['_invariants_']);
        self::assertArrayHasKey('name', $node['_invariants_'][1]);
        self::assertArrayNotHasKey('foo', $node['_invariants_'][0]);
        self::assertSame('facet', $node['_invariants_'][0]['name']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyNode(): void
    {
        $crawler = new Crawler($this->getEmptyXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new InvariantsVisitor())->visit($crawler, $closure, $node);

        self::assertCount(0, $node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNoInvariants(): void
    {
        $crawler = new Crawler($this->getNoInvaariantsXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new InvariantsVisitor())->visit($crawler, $closure, $node);

        self::assertArrayNotHasKey('_invariants_', $node);
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
    private function getNoInvaariantsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<lst name="henkelmans">
      <str name="facet">true</str>
      <long name="facet.limit">5</long>
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
