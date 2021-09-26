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
        $status = json_encode(['name' => 'foo', 'startTime' => '1973-11-01T00:00:00P']);
        $serializer = new SolrSerializer();

        $result = $serializer->deserialize($status, Status::class, 'solr');

        self::assertInstanceOf(\DateTime::class, $result->getStartTime());
    }
}
