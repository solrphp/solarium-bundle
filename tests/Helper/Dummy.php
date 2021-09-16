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
use Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\CommonGramsFilter;

/**
 * Dummy.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class Dummy
{
    /**
     * @var string[]
     */
    private static $interfaces = [
        '\\'.FilterInterface::class => CommonGramsFilter::class,
    ];

    /**
     * @param \Roave\BetterReflection\Reflection\ReflectionProperty $property
     * @param bool                                                  $includeNull
     *
     * @return false|float|int|object|string|null
     *
     * @throws \ReflectionException
     * @throws \Roave\BetterReflection\Reflector\Exception\IdentifierNotFound
     * @throws \RuntimeException
     */
    public static function getValue(ReflectionProperty $property, bool $includeNull = false)
    {
        if ($includeNull && $property->allowsNull()) {
            return null;
        }

        $namedType = (string) $property->getType();

        switch ($namedType) {
            case '':
            case 'int':
            case 'string':
            case 'bool':
            case 'float':
                return self::scalarToValue($namedType);
            case 'array':
            case ArrayCollection::class:
                $types = $property->getDocBlockTypes();

                if (!isset($types[0])) {
                    return null;
                }

                $valueType = (string) $types[0]->getValueType();

                if (\array_key_exists($valueType, self::$interfaces)) {
                    $class = self::$interfaces[$valueType];

                    return self::reflect(new $class());
                }

                if (class_exists($valueType)) {
                    return self::reflect(new $valueType());
                }

                return self::scalarToValue($valueType);
            default:
                if (class_exists($namedType)) {
                    return self::reflect(new $namedType());
                }

                throw new \RuntimeException(sprintf('unable to generate dummy value for %s', $namedType));
        }
    }

    /**
     * @param string $type
     *
     * @return false|float|int|string|null
     */
    public static function scalarToValue(string $type)
    {
        switch ($type) {
            case '':
                return 'qux';
            case 'int':
                return 1;
            case 'string':
                return 'foo';
            case 'bool':
                return false;
            case 'float':
                return 0.2;
            default:
                return null;
        }
    }

    /**
     * @param object $class
     *
     * @return object
     *
     * @throws \ReflectionException
     * @throws \Roave\BetterReflection\Reflector\Exception\IdentifierNotFound
     * @throws \RuntimeException
     */
    public static function reflect(object $class): object
    {
        $refClass = new \ReflectionClass($class);

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            $better = ReflectionProperty::createFromInstance($class, $property->getName());
            $value = self::getValue($better);
            $type = $property->getType();

            if (null !== $type && 'array' === $type->getName()) {
                $value = [$value];
            } elseif (null !== $type && ArrayCollection::class === $type->getName()) {
                $value = new ArrayCollection([$value]);
            }

            $property->setAccessible(true);
            $property->setValue($class, $value);
        }

        return $class;
    }
}
