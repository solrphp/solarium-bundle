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
use Solrphp\SolariumBundle\Common\Util\ArrayUtil;

/**
 * ArrayUtilTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ArrayUtilTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFilter(): void
    {
        $array = [
            'foo' => '',
            'bar' => null,
            'baz' => 0,
            'qux' => '0',
            'quux' => [],
            'woz' => [
                'foo' => 'bar',
            ],
        ];

        $result = ArrayUtil::filter($array);

        self::assertArrayNotHasKey('foo', $result);
        self::assertArrayNotHasKey('bar', $result);
        self::assertArrayNotHasKey('quux', $result);

        self::assertArrayHasKey('baz', $result);
        self::assertArrayHasKey('qux', $result);
        self::assertArrayHasKey('woz', $result);

        self::assertSame(0, $result['baz']);
        self::assertSame('0', $result['qux']);
        self::assertSame(['foo' => 'bar'], $result['woz']);
    }
}
