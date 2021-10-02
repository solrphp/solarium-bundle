<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Util;

/**
 * ArrayUtil.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ArrayUtil
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param array<array-key, int|float|string|bool|array<mixed>> $array
     *
     * @return array<array-key, mixed>
     */
    public static function filter(array $array): array
    {
        return array_filter($array, static fn ($val) => null !== $val && '' !== $val && (false === \is_array($val) || \count($val)));
    }
}
