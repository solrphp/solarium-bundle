<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\SolrApi\Config\CopyField;
use Solrphp\SolariumBundle\SolrApi\Config\FieldType;
use Solrphp\SolariumBundle\SolrApi\Config\ManagedSchema;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
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
     * @param array<int, array> $schemas
     *
     * @return \Generator<int, \Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface>
     */
    public function generate(array $schemas): \Generator
    {
        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter(), null, new ReflectionExtractor())]);

        foreach ($schemas as $schema) {
            foreach ($schema['fields'] as $name => $field) {
                $schema['fields'][$name] = $serializer->denormalize($field, FieldType::class);
            }

            foreach ($schema['dynamic_fields'] as $name => $field) {
                $schema['dynamic_fields'][$name] = $serializer->denormalize($field, FieldType::class);
            }

            foreach ($schema['copy_fields'] as $index => $copyField) {
                $schema['copy_fields'][$index] = $serializer->denormalize($copyField, CopyField::class);
            }

            yield new ManagedSchema($schema['unique_key'], new ArrayCollection($schema['cores']), new ArrayCollection($schema['fields']), new ArrayCollection($schema['copy_fields']), new ArrayCollection($schema['dynamic_fields']));
        }
    }
}
