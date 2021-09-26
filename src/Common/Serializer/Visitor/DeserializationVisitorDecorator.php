<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer\Visitor;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

/**
 * Deserialization Visitor Decorator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DeserializationVisitorDecorator implements DeserializationVisitorInterface
{
    /**
     * @var \JMS\Serializer\Visitor\DeserializationVisitorInterface
     */
    private DeserializationVisitorInterface $visitor;

    /**
     * @var \Closure
     */
    private \Closure $prepareData;

    /**
     * @param \JMS\Serializer\Visitor\DeserializationVisitorInterface $visitor
     * @param \Closure                                                $prepareData
     */
    public function __construct(DeserializationVisitorInterface $visitor, \Closure $prepareData)
    {
        $this->visitor = $visitor;
        $this->prepareData = $prepareData;
    }

    /**
     * {@inheritdoc}
     */
    public function visitNull($data, array $type)
    {
        return $this->visitor->visitNull($data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function visitString($data, array $type): string
    {
        return $this->visitor->visitString($data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function visitBoolean($data, array $type): bool
    {
        return $this->visitor->visitBoolean($data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function visitDouble($data, array $type): float
    {
        return $this->visitor->visitDouble($data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function visitInteger($data, array $type): int
    {
        return $this->visitor->visitInteger($data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function visitDiscriminatorMapProperty($data, ClassMetadata $metadata): string
    {
        return $this->visitor->visitDiscriminatorMapProperty($data, $metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray($data, array $type): array
    {
        return $this->visitor->visitArray($data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingObject(ClassMetadata $metadata, object $data, array $type): void
    {
        $this->visitor->startVisitingObject($metadata, $data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        return $this->visitor->visitProperty($metadata, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, array $type): object
    {
        return $this->visitor->endVisitingObject($metadata, $data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult($data)
    {
        return $this->visitor->getResult($data);
    }

    /**
     * {@inheritdoc}
     */
    public function setNavigator(GraphNavigatorInterface $navigator): void
    {
        $this->visitor->setNavigator($navigator);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($data)
    {
        return $this->prepareData->__invoke($data);
    }
}
