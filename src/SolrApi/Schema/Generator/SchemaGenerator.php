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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Schema Generator.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class SchemaGenerator
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private Serializer $serializer;

    /**
     * construct.
     */
    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $discriminator = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);
        $this->serializer = new Serializer([new ArrayDenormalizer(), new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter(), null, new ReflectionExtractor(), $discriminator)]);
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
                $schema['fields'][$name] = $this->serializer->denormalize($field, Field::class);
            }

            foreach ($schema['dynamic_fields'] as $name => $field) {
                $schema['dynamic_fields'][$name] = $this->serializer->denormalize($field, Field::class);
            }

            foreach ($schema['copy_fields'] as $index => $copyField) {
                $schema['copy_fields'][$index] = $this->serializer->denormalize($copyField, CopyField::class);
            }

            foreach ($schema['field_types'] as $index => $copyField) {
                $schema['field_types'][$index] = $this->serializer->denormalize($copyField, FieldType::class);
            }

            yield new ManagedSchema($schema['unique_key'], new ArrayCollection($schema['cores']), new ArrayCollection($schema['fields']), new ArrayCollection($schema['copy_fields']), new ArrayCollection($schema['dynamic_fields']), new ArrayCollection($schema['field_types']));
        }
    }
}
