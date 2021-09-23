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
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionProperty;
use Solrphp\SolariumBundle\Common\Response\Error;
use Solrphp\SolariumBundle\Common\Response\Header;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\CommonGramsFilter;

/**
 * Dummy.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Dummy
{
    /**
     * @var string[]
     */
    private static $interfaces = [
        '\\'.FilterInterface::class => CommonGramsFilter::class,
        '\\'.ResponseHeaderInterface::class => Header::class,
        '\\'.ResponseErrorInterface::class => Error::class,
    ];

    /**
     * @param string $class
     * @param bool   $includeNullable
     * @param bool   $initComposite
     *
     * @return array
     *
     * @throws \ReflectionException
     * @throws \Roave\BetterReflection\Reflector\Exception\IdentifierNotFound
     * @throws \RuntimeException
     */
    public static function properties(string $class, bool $includeNullable = true, bool $initComposite = true): array
    {
        $properties = [];
        $refClass = (new BetterReflection())->classReflector()->reflect($class);

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if (false === $includeNullable && $property->allowsNull()) {
                continue;
            }

            if (false === $includeNullable && \in_array($property->getType()->getName(), ['array', ArrayCollection::class], true)) {
                continue;
            }

            $value = $initComposite || !class_exists((string) $property->getType()) ? self::getValue($property, $includeNullable, $initComposite) : (string) $property->getType();
            $properties[$property->getName()] = $value;
        }

        return $properties;
    }

    /**
     * @param \Roave\BetterReflection\Reflection\ReflectionProperty $property
     * @param bool                                                  $includeNullable
     * @param bool                                                  $initComposite
     *
     * @return false|float|int|object|string|null
     *
     * @throws \ReflectionException
     * @throws \Roave\BetterReflection\Reflector\Exception\IdentifierNotFound
     * @throws \RuntimeException
     */
    public static function getValue(ReflectionProperty $property, bool $includeNullable = true, bool $initComposite = true)
    {
        if (!$includeNullable && $property->allowsNull()) {
            return null;
        }

        if (!$includeNullable && \in_array((string) $property->getType(), ['array', ArrayCollection::class], true)) {
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

                    return $initComposite ? self::reflect(new $class()) : $class;
                }

                if (class_exists($valueType)) {
                    return $initComposite ? self::reflect(new $valueType()) : $valueType;
                }

                return self::scalarToValue($valueType);
            default:
                if (class_exists($namedType)) {
                    if (\array_key_exists($namedType, self::$interfaces)) {
                        $class = self::$interfaces[$namedType];

                        return $initComposite ? self::reflect(new $class()) : $class;
                    }

                    return $initComposite ? self::reflect(new $namedType()) : $namedType;
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
                return random_int(1, 5);
            case 'string':
                return 'foo';
            case 'bool':
                return false;
            case 'float':
                return mt_rand() / mt_getrandmax();
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
