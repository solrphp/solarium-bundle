<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Serializer;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status;

/**
 * SolrSerializer Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrSerializerTest extends TestCase
{
    /**
     * @throws \JMS\Serializer\Exception\RuntimeException
     */
    public function testSerialization(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Solrphp\SolariumBundle\Common\Serializer\SolrSerializer is not configured for serialization');

        (new SolrSerializer())->serialize('', 'foo');
    }

    /**
     * @throws \JMS\Serializer\Exception\RuntimeException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testHandlers(): void
    {
        $status = json_encode(['name' => 'foo', 'start_time' => '2006-02-13T15:26:37Z']);
        $serializer = new SolrSerializer();

        $result = $serializer->deserialize($status, Status::class, 'json');
        $dt = $result->getStartTime();

        self::assertInstanceOf(\DateTime::class, $dt);
        self::assertSame('2006-02-13 15:26:37', $dt->format('Y-m-d H:i:s'));
    }
}
