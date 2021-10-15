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
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor\ParametersVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Parameters Visitor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParametersVisitorTest extends TestCase
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

        (new ParametersVisitor())->visit($crawler, $closure, $node);

        self::assertArrayHasKey('parameters', $node);
        self::assertCount(2, $node['parameters']);
        self::assertArrayHasKey('name', $node['parameters'][1]);
        self::assertArrayNotHasKey('foo', $node['parameters'][0]);
        self::assertSame('facet', $node['parameters'][0]['name']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyNode(): void
    {
        $crawler = new Crawler($this->getEmptyXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new ParametersVisitor())->visit($crawler, $closure, $node);

        self::assertCount(0, $node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNoParameters(): void
    {
        $crawler = new Crawler($this->getNoParameterssXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new ParametersVisitor())->visit($crawler, $closure, $node);

        self::assertArrayNotHasKey('parameters', $node);
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
      <lst name="foo">
        <long name="v">3</long>
      </lst>
    </lst>
XML;
    }

    /**
     * @return string
     */
    private function getNoParameterssXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>

<lst name="responseHeader">
  <int name="status">0</int>
  <int name="QTime">0</int>
</lst>
<lst name="response">
  <int name="znodeVersion">0</int>
  <lst name="params">
    <lst name="henkelmans">
      <lst name="_invariants_">
        <bool name="facet">true</bool>
        <str name="foo">bar</str>
      </lst>
      <lst name="foo">
        <long name="v">3</long>
      </lst>
    </lst>
  </lst>
</lst>
</response>
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
