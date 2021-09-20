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

/**
 * Accessor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Accessor
{
    /**
     * @param \Roave\BetterReflection\Reflection\ReflectionProperty $property
     *
     * @return string
     */
    public static function reader(ReflectionProperty $property): string
    {
        if (null === $type = $property->getType()) {
            return sprintf('get%s', ucfirst($property->getName()));
        }

        /** @var \ReflectionNamedType $type */
        if ('bool' !== $type->getName() || true === $type->allowsNull()) {
            return sprintf('get%s', ucfirst($property->getName()));
        }

        return sprintf('is%s', ucfirst($property->getName()));
    }

    /**
     * @param \Roave\BetterReflection\Reflection\Adapter\ReflectionProperty $property
     *
     * @return string
     */
    public static function writer(ReflectionProperty $property): string
    {
        if (null === $property->getType()) {
            return sprintf('set%s', ucfirst($property->getName()));
        }

        return self::isIterable($property) ? sprintf('add%s', ucfirst(rtrim($property->getName(), 's'))) : sprintf('set%s', ucfirst($property->getName()));
    }

    /**
     * @param \Roave\BetterReflection\Reflection\ReflectionProperty $property
     *
     * @return string
     */
    public static function remover(ReflectionProperty $property): string
    {
        return sprintf('remove%s', ucfirst(rtrim($property->getName(), 's')));
    }

    /**
     * @param string $class
     *
     * @return array<string, array>
     *
     * @throws \Roave\BetterReflection\Reflector\Exception\IdentifierNotFound
     */
    public static function all(string $class): array
    {
        $refClass = (new BetterReflection())->classReflector()->reflect($class);

        $accessors = [];

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            $accessors[$property->getName()] = [
                'reader' => self::reader($property),
                'writer' => self::writer($property),
                'remover' => self::isIterable($property) ? self::remover($property) : null,
            ];
        }

        return $accessors;
    }

    /**
     * @param \Roave\BetterReflection\Reflection\ReflectionProperty $property
     *
     * @return bool
     */
    private static function isIterable(ReflectionProperty $property): bool
    {
        if (null === $type = $property->getType()) {
            return false;
        }

        /** @var \ReflectionNamedType $type */
        $namedType = $type->getName();

        return !('array' !== $namedType && ArrayCollection::class !== $namedType);
    }
}
