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
use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter\PatternReplaceCharFilter;

/**
 * Object Util.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ObjectUtil
{
    /**
     * @param object $class
     * @param bool   $populateNull
     * @param string $accessor
     *
     * @return array
     */
    public static function properties(object $class, bool $populateNull = true, string $accessor = 'get'): array
    {
        $refClass = new \ReflectionClass(\get_class($class));
        $return = [];

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if (false === $populateNull && $property->getType()->allowsNull()) {
                continue;
            }

            $name = $property->getName();
            $return[$name] = sprintf('%s%s', $accessor, ucfirst($name));
        }

        return $return;
    }

    /**
     * @param object $class
     * @param bool   $includeNullable
     *
     * @return object|\JsonSerializable
     */
    public static function reflect(object $class, bool $includeNullable = true): object
    {
        $refClass = new \ReflectionClass(\get_class($class));

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if (false === $includeNullable && (null !== $type = $property->getType()) && $type->allowsNull()) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($class, self::getValue($property, $includeNullable));
        }

        return $class;
    }

    /**
     * @param \ReflectionProperty $property
     * @param bool                $populateNull
     *
     * @return array|\Doctrine\Common\Collections\ArrayCollection|false|int|object|string
     *
     * @throws \ReflectionException
     */
    public static function getValue(\ReflectionProperty $property, bool $populateNull = true)
    {
        // mixed types get a string.
        if (null === $type = $property->getType()) {
            return 'qux';
        }

        /** @var \ReflectionNamedType $type */
        switch ($type->getName()) {
            case 'int':
            case 'string':
            case 'bool':
            case 'float':
                return self::getDummyScalarValue($type->getName());
            case 'array':
            case ArrayCollection::class:
                $value = [];

                if (null !== $class = self::getTypeFromDocComment($property)) {
                    if (class_exists($class) || interface_exists($class)) {
                        $value = [self::reflect(self::getObjectFromClassString($class), $populateNull)];
                    } else {
                        $value = [self::getDummyScalarValue($class)];
                    }
                }

                return 'array' === $type->getName() ? $value : new ArrayCollection($value);
            default:
                $class = $type->getName();

                return self::reflect(self::getObjectFromClassString($class), $populateNull);
        }
    }

    /**
     * @param string $scalar
     *
     * @return false|float|int|string
     */
    public static function getDummyScalarValue(string $scalar)
    {
        switch ($scalar) {
            case 'int':
                return 6;
            case 'string':
                return 'foo';
            case 'bool':
                return false;
            case 'float':
                return 2.3;
        }

        throw new \LogicException(sprintf('no such scalar %s', $scalar));
    }

    /**
     * @param string $class
     *
     * @return object|null
     *
     * @throws \ReflectionException
     */
    public static function getObjectFromClassString(string $class): ?object
    {
        $refClass = new \ReflectionClass($class);

        if (!$refClass->isAbstract()) {
            return $refClass->newInstanceWithoutConstructor();
        }

        if (FilterInterface::class === $refClass->getName()) {
            return new PatternReplaceCharFilter();
        }

        return null;
    }

    /**
     * @param \ReflectionProperty $property
     *
     * @return string|null
     */
    public static function getTypeFromDocComment(\ReflectionProperty $property): ?string
    {
        switch ($property->getType()->getName()) {
            case 'array':
                $pattern = '/@var ([^\n]+)\[\]\n/';

                break;
            case ArrayCollection::class:
                $pattern = '/@var ArrayCollection<[^,]?,{0,1}([^>])>\n/';

                break;
            default:
                $pattern = '/@var ([^\n]+)\n/';
        }

        preg_match($pattern, $property->getDocComment(), $matches);

        return $matches[1] ?? null;
    }

    /**
     * @param string $property
     * @param bool   $plural
     *
     * @return array<string, string>
     */
    public static function methods(string $property, bool $plural = true): array
    {
        $cased = ucfirst($property);

        return [
            sprintf('add%s', $cased),
            sprintf('remove%s', $cased),
            sprintf('set%s%s', $cased, $plural ? 's' : ''),
            sprintf('get%s%s', $cased, $plural ? 's' : ''),
        ];
    }
}
