<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Visitor\FieldType;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\CharFilterFieldTypeVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * CharFilterFieldTypeVisitorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CharFilterFieldTypeVisitorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testContinue(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);

        $filter = array_combine(CharFilterFieldTypeVisitor::$attributes, CharFilterFieldTypeVisitor::$attributes);

        $crawler = $this->getMockBuilder(Crawler::class)->getMock();
        $crawler->expects(self::once())->method('filterXPath')->willReturnSelf();
        $crawler->expects(self::once())->method('extract')->willReturn(['foo' => ['bar' => 'baz'], 'qux' => $filter]);

        $nodes = [];

        (new CharFilterFieldTypeVisitor())->visit($crawler, $closure, $nodes);

        self::assertCount(1, $nodes);
    }
}
