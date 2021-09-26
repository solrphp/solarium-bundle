<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

/**
 * Solr Serializer.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrSerializer implements SerializerInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->addDefaultSerializationVisitors()
            ->addDefaultDeserializationVisitors()
            ->setAnnotationReader(new AnnotationReader())
            ->setPropertyNamingStrategy(new CamelCaseNamingStrategy())
            ->addMetadataDir(__DIR__.'/../../Resources/serializer/schema')
            ->build()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?string $type = null): string
    {
        return $this->serializer->serialize($data, $format, $context, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize(string $data, string $type, string $format, ?DeserializationContext $context = null)
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}
