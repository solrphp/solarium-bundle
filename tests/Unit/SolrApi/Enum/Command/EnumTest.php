<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Enum\Command;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Enum\Command\Config;
use Solrphp\SolariumBundle\SolrApi\Enum\Command\Schema;
use Solrphp\SolariumBundle\SolrApi\Enum\SubPath\Config as SubPathConfig;
use Solrphp\SolariumBundle\SolrApi\Enum\SubPath\Schema as SubPathSchema;

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
        $refClass = new \ReflectionClass(Config::class);

        /** @var \PHPStan\BetterReflection\Reflection\ReflectionConstant $constant */
        foreach ($refClass->getConstants() as $constant) {
            if (true === \is_array($constant)) {
                continue;
            }

            self::assertArrayHasKey($constant, Config::COMMANDS);
        }
    }

    /**
     * test command schema availability.
     */
    public function testCommandSchemaAvailability(): void
    {
        $refClass = new \ReflectionClass(Schema::class);

        /** @var \PHPStan\BetterReflection\Reflection\ReflectionConstant $constant */
        foreach ($refClass->getConstants() as $constant) {
            if (true === \is_array($constant)) {
                continue;
            }

            self::assertArrayHasKey($constant, Schema::COMMANDS);
        }
    }

    /**
     * test sub path config availability.
     */
    public function testSubPathConfigAvailability(): void
    {
        $refClass = new \ReflectionClass(SubPathConfig::class);

        foreach ($refClass->getConstants() as $constant) {
            if (true === \is_array($constant) || '' === $constant) {
                continue;
            }

            self::assertContains($constant, SubPathConfig::SUB_PATHS);
        }
    }

    /**
     * test sub path schema availability.
     */
    public function testSubPathSchemaAvailability(): void
    {
        $refClass = new \ReflectionClass(SubPathSchema::class);

        foreach ($refClass->getConstants() as $constant) {
            if (true === \is_array($constant) || '' === $constant) {
                continue;
            }

            self::assertContains($constant, SubPathSchema::SUB_PATHS);
        }
    }
}
