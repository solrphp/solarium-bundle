<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Serializer\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Handler\SolrDateHandler;

/**
 * SolrDate Handler Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrDateHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSubscribingMethods(): void
    {
        $subs = SolrDateHandler::getSubscribingMethods();

        self::assertSame(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, $subs[0]['direction']);
        self::assertSame('json', $subs[0]['format']);
        self::assertSame(\DateTime::class, $subs[0]['type']);
        self::assertSame(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, $subs[1]['direction']);
        self::assertSame('solr', $subs[1]['format']);
        self::assertSame(\DateTime::class, $subs[1]['type']);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDeserializeDate(): void
    {
        $handler = new SolrDateHandler();
        $context = SerializationContext::create();
        $visitor = new JsonDeserializationVisitor();

        $data = '2021-09-26T15:16:08Z';

        $result = $handler->deserializeDateTime($visitor, $data, [], $context);
        self::assertInstanceOf(\DateTime::class, $result);
        self::assertSame($data, sprintf('%s%s', strstr($result->format(\DateTime::ATOM), '+', true), 'Z'));
    }
}
