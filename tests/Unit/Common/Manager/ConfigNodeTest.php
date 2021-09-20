<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Manager\ConfigNode;

/**
 * ConfigNode Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigNodeTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConfigNodeConstruction(): void
    {
        $node = new ConfigNode('Foo\Bar', '/baz', new ArrayCollection(['foo']));

        self::assertSame('/baz', $node->getPath());
        self::assertSame('Foo\Bar', $node->getType());
        self::assertContains('foo', $node->get());
    }
}
