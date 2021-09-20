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

use Roave\BetterReflection\Reflection\ReflectionProperty;

/**
 * RefClass.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RefClass
{
    /**
     * @param string $class
     * @param bool   $includeNullable
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public static function properties(string $class, bool $includeNullable = true): array
    {
        $refClass = new \ReflectionClass($class);

        $properties = [];

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if (!$includeNullable && $property->getType()->allowsNull()) {
                continue;
            }

            $better = ReflectionProperty::createFromName($class, $property->getName());
            $properties[$property->getName()] = Accessor::reader($better);
        }

        return $properties;
    }

    /**
     * @param string $class
     *
     * @return \ArrayObject
     *
     * @throws \ReflectionException
     */
    public static function composites(string $class): \ArrayObject
    {
        $refClass = new \ReflectionClass($class);
        $composites = [$class => $refClass->newInstanceWithoutConstructor()];

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if (null === $type = $property->getType()) {
                continue;
            }

            $name = $type->getName();

            if (class_exists($name)) {
                $composites[$name] = new $name();
            }
        }

        return new \ArrayObject($composites);
    }
}
