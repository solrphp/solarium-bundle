<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Util;

/**
 * ConfigUtil.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ConfigUtil
{
    public const DEFAULT_SEPARATOR = '.';

    /**
     * @param object      $object
     * @param string|null $prefix
     * @param string|null $separator
     *
     * @return array<string, mixed>
     */
    public static function toPropertyPaths(object $object, ?string $prefix = null, ?string $separator = null): array
    {
        $refClass = new \ReflectionClass($object);
        $separator = $separator ?? self::DEFAULT_SEPARATOR;

        $array = [];

        foreach ($refClass->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);

            $name = (null !== $prefix) ? sprintf('%s%s%s', $prefix, $separator, $property->getName()) : $property->getName();

            if (true === \is_array($value)) {
                foreach ($value as $key => $composite) {
                    self::processNode($array, $name.$separator.$key, $composite, $separator);
                }
            } else {
                self::processNode($array, $name, $value, $separator);
            }
        }

        return array_merge(...$array);
    }

    /**
     * @param array<string, string> $array
     * @param string                $name
     * @param object|string         $value
     * @param string                $separator
     */
    private static function processNode(array &$array, string $name, $value, string $separator): void
    {
        if (true === \is_object($value)) {
            $array[] = self::toPropertyPaths($value, $name, $separator);
        } else {
            $array[][$name] = $value;
        }
    }
}
