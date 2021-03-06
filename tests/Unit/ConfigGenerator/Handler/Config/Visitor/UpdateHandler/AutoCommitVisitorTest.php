<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Config\Visitor\UpdateHandler;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\UpdateHandler\AutoCommitVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * AutoCommitVisitorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AutoCommitVisitorTest extends TestCase
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

        (new AutoCommitVisitor())->visit($crawler, $closure, $node);

        self::assertArrayHasKey('auto_commit', $node);
        self::assertArrayHasKey('max_time', $node['auto_commit']);
        self::assertArrayNotHasKey('max_size', $node['auto_commit']);
        self::assertSame('${solr.autoCommit.maxTime:15000}', $node['auto_commit']['max_time']);
    }

    /**
     * @return string
     */
    private function getXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" ?>
<config>
  <updateHandler class="solr.DirectUpdateHandler2" numVersionBuckets="2">
    <updateLog>
      <str name="dir">${solr.ulog.dir:}</str>
      <int name="numVersionBuckets">${solr.ulog.numVersionBuckets:65536}</int>
    </updateLog>
    <autoCommit>
      <maxTime>${solr.autoCommit.maxTime:15000}</maxTime>
      <openSearcher>false</openSearcher>
    </autoCommit>
    <autoSoftCommit>
      <maxTime>${solr.autoSoftCommit.maxTime:-1}</maxTime>
    </autoSoftCommit>
    <commitWithin>
      <softCommit>${solr.commitwithin.softcommit:true}</softCommit>
    </commitWithin>
  </updateHandler>
</config>
XML;
    }
}
