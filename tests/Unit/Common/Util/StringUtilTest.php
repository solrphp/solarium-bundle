<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Util;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Util\StringUtil;

/**
 * StringUtilTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StringUtilTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testXml(): void
    {
        $str = '<?xml version="1.0" encoding="UTF-8"?><schema name="default-config" version="1.6"><uniqueKey>id</uniqueKey><fieldType name="_nest_path_" class="solr.NestPathField" omitTermFreqAndPositions="true" omitNorms="true" maxCharsForDocValues="-1" stored="false" multiValued="false"/></schema>';

        self::assertSame($this->getXml(), StringUtil::prettyXml($str));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyXml(): void
    {
        self::assertSame('', StringUtil::prettyXml(null));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testInvalidXml(): void
    {
        self::assertSame('{]', StringUtil::prettyJson('{]'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testJson(): void
    {
        $str = '{"docs": [ { "id": "GB18030TEST", "name": [ "Test with some GB18030 encoded characters" ], "features": [ "No accents here", "这是一个功能", "This is a feature (translated)", "这份文件是很有光泽", "This document is very shiny (translated)" ], "price": [ 0.0 ], "inStock": [ true ], "_version_": 1711261211662745600, "score": 1.0 } ] }';

        self::assertSame($this->getJson(), StringUtil::prettyJson($str));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyJson(): void
    {
        self::assertSame('', StringUtil::prettyJson(null));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testInvalidJson(): void
    {
        self::assertSame('{]', StringUtil::prettyJson('{]'));
    }

    /**
     * @return string
     */
    private function getXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<schema name="default-config" version="1.6">
  <uniqueKey>id</uniqueKey>
  <fieldType name="_nest_path_" class="solr.NestPathField" omitTermFreqAndPositions="true" omitNorms="true" maxCharsForDocValues="-1" stored="false" multiValued="false"/>
</schema>

XML;
    }

    /**
     * @return string
     */
    private function getJson(): string
    {
        return <<<'JSON'
{
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
            "price": [
                0.0
            ],
            "inStock": [
                true
            ],
            "_version_": 1711261211662745600,
            "score": 1.0
        }
    ]
}
JSON;
    }
}
