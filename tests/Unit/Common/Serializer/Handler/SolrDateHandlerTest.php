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

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Handler\SolrDateHandler;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;

/**
 * Solr DateHandler Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrDateHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGetSubscribingMethods(): void
    {
        $subs = SolrDateHandler::getSubscribingMethods();

        self::assertSame(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, $subs[0]['direction']);
        self::assertSame('json', $subs[0]['format']);
        self::assertSame(\DateTime::class, $subs[0]['type']);
    }

    /**
     * @throws \JMS\Serializer\Exception\RuntimeException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDeserialize(): void
    {
        $result = (new SolrSerializer())
            ->deserialize($this->getJson(), ResponseInterface::class, 'json', DeserializationContext::create()->setAttribute('solrphp.real_class', StatusResponse::class));

        self::assertInstanceOf(\DateTime::class, $result->getStatus()[0]->getStartTime());
    }

    /**
     * @return string
     */
    private function getJson(): string
    {
        return <<<'JSON'
{
"responseHeader":
  { "status":0, "QTime":2},
"initFailures":{},
"status":
  { "demo":
    { "name":"demo", "instanceDir":"/var/solr/data/demo", "dataDir":"/var/solr/data/demo/data/", "config":"solrconfig.xml", "schema":"managed-schema", "startTime":"2021-09-29T15:53:08.693Z", "uptime":37780377,
      "index":{ "numDocs":46, "maxDoc":46, "deletedDocs":0, "indexHeapUsageBytes":6116, "version":6, "segmentCount":1, "current":true, "hasDeletions":false, "directory":"org.apache.lucene.store.NRTCachingDirectory:NRTCachingDirectory(MMapDirectory@/var/solr/data/demo/data/index lockFactory=org.apache.lucene.store.NativeFSLockFactory@4e019a75; maxCacheMB=48.0 maxMergeSizeMB=4.0)", "segmentsFile":"segments_2", "segmentsFileSizeInBytes":220,
      "userData":{ "commitCommandVer":"1711261212601221120", "commitTimeMSec":"1631985867121"}
    }
  }
}
}
JSON;
    }
}
