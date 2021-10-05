<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Exception\UnexpectedValueException;

/**
 * Unexpected Value Exception Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UnexpectedValueExceptionTest extends TestCase
{
    /**
     * test unexpected value exception.
     */
    public function testUnexpectedValueException(): void
    {
        $previous = new \RuntimeException();
        $exception = new UnexpectedValueException('foo', $previous);

        self::assertSame('foo', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
