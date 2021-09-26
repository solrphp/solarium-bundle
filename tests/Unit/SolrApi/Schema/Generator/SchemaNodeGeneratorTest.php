<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Generator;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;

/**
 * ConfigNode Generator Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaNodeGeneratorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGet(): void
    {
        $field = new Field();
        $copyField = new CopyField();
        $dynamicField = new Field();
        $fieldType = new FieldType();

        $schema = new ManagedSchema('id', ['foo'], [$field], [$copyField], [$dynamicField], [$fieldType]);

        $result = [
            [
                'type' => Field::class,
                'path' => SubPath::LIST_FIELDS,
                'first' => $field,
            ],
            [
                'type' => Field::class,
                'path' => SubPath::LIST_DYNAMIC_FIELDS,
                'first' => $dynamicField,
            ],
            [
                'type' => CopyField::class,
                'path' => SubPath::LIST_COPY_FIELDS,
                'first' => $copyField,
            ],
            [
                'type' => FieldType::class,
                'path' => SubPath::LIST_FIELD_TYPES,
                'first' => $fieldType,
            ],
        ];

        foreach ((new SchemaNodeGenerator())->get($schema) as $key => $configNode) {
            self::assertSame($result[$key]['type'], $configNode->getType());
            self::assertSame($result[$key]['path'], $configNode->getPath());
            foreach ($configNode->get() as $value) {
                break;
            }
            self::assertSame($result[$key]['first'], $value);
        }

        // making sure all nodes are returned
        self::assertSame(3, $key);
    }
}
