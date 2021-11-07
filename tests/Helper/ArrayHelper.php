<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Helper;

/**
 * Value.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ArrayHelper
{
    /**
     * @param array $array
     *
     * @return array
     */
    public static function keysort(array $array): array
    {
        ksort($array);

        return $array;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public static function ksortnormalized(array $array): array
    {
        $array = self::keysort($array);

        return array_map(
            static function ($key) {
                return 'useParams' === $key ? $key : strtolower(preg_replace('/(?<=[a-z])(?=[A-Z])/', '-', $key));
            },
            array_keys($array)
        );
    }
}
