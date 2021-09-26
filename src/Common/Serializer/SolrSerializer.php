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

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Solrphp\SolariumBundle\Common\Serializer\Handler\PropertyListHandler;
use Solrphp\SolariumBundle\Common\Serializer\Visitor\PrepareCallable;
use Solrphp\SolariumBundle\Common\Serializer\Visitor\SolrDeserializationVisitorFactory;

/**
 * Solr Serializer.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrSerializer implements SerializerInterface
{
    /**
     * @var \JMS\Serializer\Serializer
     */
    private Serializer $serializer;

    /**
     * @throws \JMS\Serializer\Exception\RuntimeException
     */
    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->addDefaultSerializationVisitors()
            ->addDefaultDeserializationVisitors()
            ->setDeserializationVisitor('solr', new SolrDeserializationVisitorFactory(\Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse'])))
            ->addDefaultHandlers()
            ->configureHandlers(static function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new PropertyListHandler());
            })
            ->addMetadataDir(__DIR__.'/../../Resources/serializer/schema')
            ->build()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?string $type = null): string
    {
        throw new \RuntimeException(sprintf('%s is not configured for serialization', __CLASS__));
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize(string $data, string $type, string $format, ?DeserializationContext $context = null)
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}
