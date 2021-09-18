<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Enum;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath;

/**
 * Enum Test.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class EnumTest extends TestCase
{
    /**
     * test command config availability.
     */
    public function testCommandConfigAvailability(): void
    {
        $refClass = new \ReflectionClass(Command::class);

        /** @var \PHPStan\BetterReflection\Reflection\ReflectionConstant $constant */
        foreach ($refClass->getConstants() as $constant) {
            if (true === \is_array($constant)) {
                continue;
            }

            self::assertArrayHasKey($constant, Command::ALL);
        }
    }

    /**
     * test sub path config availability.
     */
    public function testSubPathConfigAvailability(): void
    {
        $refClass = new \ReflectionClass(SubPath::class);

        foreach ($refClass->getConstants() as $constant) {
            if (true === \is_array($constant) || '' === $constant) {
                continue;
            }

            self::assertContains($constant, SubPath::ALL);
        }
    }
}
