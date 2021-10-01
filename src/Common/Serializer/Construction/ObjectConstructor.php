<?php

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer\Construction;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use ReflectionClass;

/**
 * ObjectConstructor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ObjectConstructor implements ObjectConstructorInterface
{
    /**
     * @param \JMS\Serializer\Visitor\DeserializationVisitorInterface $visitor
     * @param \JMS\Serializer\Metadata\ClassMetadata                  $metadata
     * @param mixed                                                   $data
     * @param array<string>                                           $type
     * @param \JMS\Serializer\DeserializationContext                  $context
     *
     * @return object|null
     *
     * @throws \ReflectionException
     */
    public function construct(DeserializationVisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context): ?object
    {
        if (false === class_exists($metadata->name)) {
            return null;
        }
        $refClass = new ReflectionClass($metadata->name);
        $constructor = $refClass->getConstructor();

        if (null !== $constructor && 0 === $constructor->getNumberOfRequiredParameters()) {
            return $refClass->newInstance();
        }

        return $refClass->newInstanceWithoutConstructor();
    }
}
