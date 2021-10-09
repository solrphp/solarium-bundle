<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Schema\Visitor\FieldType;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Schema\Visitor\FieldType\TokenizerFieldTypeVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * TokenizerFieldTypeVisitorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class TokenizerFieldTypeVisitorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testContinue(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $filter = array_combine(TokenizerFieldTypeVisitor::$attributes, TokenizerFieldTypeVisitor::$attributes);

        $crawler = $this->getMockBuilder(Crawler::class)->getMock();
        $crawler->expects(self::once())->method('filterXPath')->willReturnSelf();
        $crawler->expects(self::once())->method('extract')->willReturn(['foo' => ['bar' => 'baz'], 'qux' => $filter]);

        $nodes = [];

        (new TokenizerFieldTypeVisitor())->visit($crawler, $closure, $nodes);

        self::assertCount(1, $nodes);
    }
}
