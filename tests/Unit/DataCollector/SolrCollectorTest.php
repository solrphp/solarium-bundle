<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\DataCollector;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\DataCollector\SolrCallRegistry;
use Solrphp\SolariumBundle\DataCollector\SolrCollector;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SolrCollectorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCollectorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCollect(): void
    {
        $collector = new SolrCollector($this->getRegistry());
        $collector->collect(new Request([]), new Response());

        self::assertSame(2, $collector->getTotal());
        self::assertCount(2, $collector->getRequests());
        self::assertSame(0.0199999809265136, $collector->getTime());
        self::assertTrue($collector->getTime() > 0);
        self::assertTrue($collector->getTime() < 0.2);

        $collector->reset();

        self::assertSame(0, $collector->getTotal());
        self::assertSame(0.0, $collector->getTime());
        self::assertCount(0, $collector->getRequests());
    }

    /**
     * @return \Solrphp\SolariumBundle\DataCollector\SolrCallRegistry
     */
    public function getRegistry(): SolrCallRegistry
    {
        $registry = $this->getMockBuilder(SolrCallRegistry::class)->getMock();

        $registry->expects(self::exactly(1))
            ->method('getCalls')
            ->willReturn($this->getCalls())
        ;

        return $registry;
    }

    /**
     * @return array[]
     */
    public function getCalls(): array
    {
        $body = <<<"JSON"
{"response": {
        "numFound": 46,
        "start": 0,
        "maxScore": 1.0,
        "numFoundExact": true,
        "docs": [
            {
                "id": "GB18030TEST",
                "name": [
                    "Test with some GB18030 encoded characters"
                ],
                "features": [
                    "No accents here",
                    "这是一个功能",
                    "This is a feature (translated)",
                    "这份文件是很有光泽",
                    "This document is very shiny (translated)"
                ],
                "_version_": 1711261211662745600,
                "score": 1.0
            },
            {
                "id": "SP2514N",
                "name": [
                    "Samsung SpinPoint P120 SP2514N - hard drive - 250 GB - ATA-133"
                ],
                "manu": [
                    "Samsung Electronics Co. Ltd."
                ],
                "_version_": 1711261211805351936,
                "score": 1.0
            }
        }
}
JSON;

        return [
            [
                'id' => 1,
                'resource' => 'http://127.0.0.1:8983/solr/demo/select',
                'request_headers' => new InputBag(['foo' => 'bar']),
                'request_options' => new InputBag(['baz' => 'qux']),
                'request_params' => new InputBag(['foo' => 'bar']),
                'request_body' => '',
                'response_body' => $body,
                'start' => 1632931133.0036,
                'end' => 1632931133.0136,
                'response_headers' => new InputBag(['foo' => 'bar']),
                'status_code' => 200,
                'duration' => 0.0099999904632568,
            ],
            [
                'id' => 3,
                'resource' => 'http://127.0.0.1:8983/solr/demo/select',
                'request_headers' => new InputBag(['baz' => 'qux']),
                'request_options' => new InputBag(['baz' => 'qux']),
                'request_params' => new InputBag(['foo' => 'bar']),
                'request_body' => $body,
                'response_body' => '',
                'start' => 1632931379.0039,
                'end' => 1632931379.0139,
                'response_headers' => new InputBag(['baz' => 'qux']),
                'status_code' => 400,
            ],
            [
                'id' => 2,
                'resource' => 'http://127.0.0.1:8983/solr/demo/select',
                'request_headers' => new InputBag(['baz' => 'qux']),
                'request_options' => new InputBag(['baz' => 'qux']),
                'request_params' => new InputBag(['foo' => 'bar']),
                'request_body' => $body,
                'response_body' => '',
                'start' => 1632931379.0039,
                'end' => 1632931379.0139,
                'response_headers' => new InputBag(['baz' => 'qux']),
                'status_code' => 400,
                'duration' => 0.0099999904632568,
            ],
        ];
    }
}
