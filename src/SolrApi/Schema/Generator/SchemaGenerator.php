<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Generator;

use JMS\Serializer\SerializerInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;

/**
 * Schema Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaGenerator
{
    /**
     * @var string
     */
    private string $format = 'json';

    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array<int, array<string, mixed>> $schemas
     *
     * @return \Generator<int, \Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface>
     */
    public function generate(array $schemas): \Generator
    {
        foreach ($schemas as $schema) {
            foreach ($schema['fields'] as $name => $field) {
                $schema['fields'][$name] = $this->serializer->deserialize(json_encode($field, \JSON_THROW_ON_ERROR), Field::class, $this->format);
            }

            foreach ($schema['dynamic_fields'] as $name => $field) {
                $schema['dynamic_fields'][$name] = $this->serializer->deserialize(json_encode($field, \JSON_THROW_ON_ERROR), Field::class, $this->format);
            }

            foreach ($schema['copy_fields'] as $index => $copyField) {
                $schema['copy_fields'][$index] = $this->serializer->deserialize(json_encode($copyField, \JSON_THROW_ON_ERROR), CopyField::class, $this->format);
            }

            foreach ($schema['field_types'] as $index => $copyField) {
                $schema['field_types'][$index] = $this->serializer->deserialize(json_encode($copyField, \JSON_THROW_ON_ERROR), FieldType::class, $this->format);
            }

            yield new ManagedSchema($schema['unique_key'], $schema['cores'], $schema['fields'], $schema['copy_fields'], $schema['dynamic_fields'], $schema['field_types']);
        }
    }
}
