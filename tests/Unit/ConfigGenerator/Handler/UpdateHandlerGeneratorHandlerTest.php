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
use Solrphp\SolariumBundle\ConfigGenerator\Handler\UpdateHandlerGeneratorHandler;
use Symfony\Component\DomCrawler\Crawler;

/**
 * UpdateHandlerGeneratorHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UpdateHandlerGeneratorHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testHandle(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getXml());

        $nodes = (new UpdateHandlerGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(5, $nodes);
        self::assertArrayHasKey('class', $nodes);
        self::assertArrayHasKey('auto_commit', $nodes);
        self::assertArrayHasKey('auto_soft_commit', $nodes);
        self::assertArrayHasKey('commit_within', $nodes);
        self::assertArrayHasKey('update_log', $nodes);

        self::assertSame('solr.DirectUpdateHandler2', $nodes['class']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyNode(): void
    {
        $crawler = new Crawler($this->getEmptyXml());
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $node = [];

        (new UpdateHandlerGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(0, $node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testBuggyCrawler(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $combine = array_combine(UpdateHandlerGeneratorHandler::$attributes, UpdateHandlerGeneratorHandler::$attributes);

        $crawler = $this->getMockBuilder(Crawler::class)->getMock();
        $crawler->expects(self::once())->method('filterXPath')->willReturnSelf();
        $crawler->expects(self::once())->method('extract')->willReturn(['foo' => ['bar' => 'baz'], 'qux' => $combine]);

        $nodes = (new UpdateHandlerGeneratorHandler([]))->handle($crawler, $closure);

        self::assertCount(2, $nodes);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        self::assertTrue((new UpdateHandlerGeneratorHandler())->supports(ConfigGenerator::TYPE_UPDATE_HANDLER));
        self::assertFalse((new UpdateHandlerGeneratorHandler())->supports(ConfigGenerator::TYPE_COPY_FIELD));
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

    /**
     * @return string
     */
    private function getEmptyXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" ?>
<config>
  <updateHandler>
  </updateHandler>
</config>
XML;
    }
}
