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

use Doctrine\Common\Collections\ArrayCollection;
use Roave\BetterReflection\Reflection\ReflectionProperty;

/**
 * Value.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class Value
{
    private static $map = [
        'int' => 'integer',
        'bool' => 'boolean',
        'string' => 'string',
        'float' => 'double',
    ];

    /**
     * @param \Roave\BetterReflection\Reflection\ReflectionProperty $property
     * @param mixed                                                 $value
     *
     * @return bool
     */
    public static function validate(ReflectionProperty $property, $value): bool
    {
        $typeString = (string) $property->getType();

        switch ($typeString) {
            case 'int':
                return \is_int($value);
            case 'string':
                return \is_string($value);
            case 'bool':
                return \is_bool($value);
            case 'float':
                return \is_float($value);
            case 'array':
            case ArrayCollection::class:
                $types = $property->getDocBlockTypes();

                if (!isset($types[0])) {
                    return false;
                }

                $valueType = (string) $types[0]->getValueType();

                if (true === class_exists($valueType) || true === interface_exists($valueType)) {
                    return $value instanceof $valueType;
                }

                return isset(self::$map[$valueType]) && \gettype($value) === self::$map[$valueType];
            default:
                if (class_exists($typeString)) {
                    return $value instanceof $typeString;
                }

                return false;
        }
    }
}
