<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\CoreAdmin\Helper\Table;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\IndexTableCreator;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Index;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\UserData;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * IndexTable Creator Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class IndexTableCreatorTest extends TestCase
{
    /**
     * @var false|resource
     */
    protected $stream;

    /**
     * set up.
     */
    protected function setUp(): void
    {
        $this->stream = fopen('php://memory', 'r+b');
    }

    /**
     * tear down.
     */
    protected function tearDown(): void
    {
        fclose($this->stream);

        $this->stream = null;
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCreate()
    {
        $userData = new UserData();
        $userData->setCommitCommandVer('foo');
        $userData->setCommitTimeMSec('bar');

        $index = new Index();
        $index->setCurrent(true);
        $index->setDeletedDocs(2);
        $index->setDirectory('foo');
        $index->setHasDeletions(true);
        $index->setIndexHeapUsageBytes(10);
        $index->setLastModified(date_create('1970-01-01 00:00:00'));
        $index->setMaxDoc(10);
        $index->setNumDocs(3);
        $index->setSegmentCount(8);
        $index->setSegmentsFile('bar');
        $index->setSize('9');
        $index->setSizeInBytes(90);
        $index->setVersion(8);
        $index->setUserData($userData);

        $status = new Status();
        $status->setName('foo');
        $status->setConfig('bar');
        $status->setDataDir('baz');
        $status->setInstanceDir('qux');
        $status->setSchema('quux');
        $status->setStartTime(date_create('1970-01-01 00:00:00'));
        $status->setUptime(3);

        $status->setIndex($index);

        $response = new StatusResponse();
        $response->addStatus($status);

        $expected = <<<TABLE
┌──────┬─────────┬────────┬─────────────┬─────────────────────┬─────────┬──────────────┬─── index ──────────────┬───────────┬──────────────┬───────────────────────────┬─────────────┬──────┐
│ core │ numDocs │ maxDoc │ deletedDocs │ indexHeapUsageBytes │ version │ segmentCount │ current │ hasDeletions │ directory │ segmentsFile │ lastModified              │ sizeInBytes │ size │
├──────┼─────────┼────────┼─────────────┼─────────────────────┼─────────┼──────────────┼─────────┼──────────────┼───────────┼──────────────┼───────────────────────────┼─────────────┼──────┤
│ foo  │ 3       │ 10     │ 2           │ 10                  │ 8       │ 8            │ 1       │ 1            │ foo       │ bar          │ 1970-01-01T00:00:00+00:00 │ 90          │ 9    │
└──────┴─────────┴────────┴─────────────┴─────────────────────┴─────────┴──────────────┴─────────┴──────────────┴───────────┴──────────────┴───────────────────────────┴─────────────┴──────┘

TABLE;

        (new IndexTableCreator())->create($output = $this->getOutputStream(), $response)->render();

        $this->assertSame($expected, $this->getOutputContent($output));
    }

    /**
     * @param false $decorated
     *
     * @return \Symfony\Component\Console\Output\StreamOutput
     */
    private function getOutputStream($decorated = false)
    {
        return new StreamOutput($this->stream, StreamOutput::VERBOSITY_NORMAL, $decorated);
    }

    /**
     * @param \Symfony\Component\Console\Output\StreamOutput $output
     *
     * @return array|false|string|string[]
     */
    private function getOutputContent(StreamOutput $output)
    {
        rewind($output->getStream());

        return str_replace(\PHP_EOL, "\n", stream_get_contents($output->getStream()));
    }
}
