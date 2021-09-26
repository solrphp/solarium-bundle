<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Serializer\Visitor;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Visitor\PrepareCallable;
use Solrphp\SolariumBundle\Common\Serializer\Visitor\SolrDeserializationVisitorFactory;

/**
 * Visitor Factory Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class VisitorFactoryTest extends TestCase
{
    /**
     * @throws \JsonException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPrepare(): void
    {
        $arr = ['key-one' => 'one', 'key-two' => ['sub-one' => 'one']];

        $factory = new SolrDeserializationVisitorFactory(\Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']));
        $visitor = $factory->getVisitor();
        $data = $visitor->prepare(json_encode($arr, \JSON_THROW_ON_ERROR));

        self::assertArrayNotHasKey('key-one', $data);
        self::assertArrayHasKey('key_one', $data);
        self::assertArrayNotHasKey('key-two', $data);
        self::assertArrayHasKey('key_two', $data);
        self::assertArrayNotHasKey('sub-one', $data['key_two']);
        self::assertArrayHasKey('sub_one', $data['key_two']);
    }
}
