<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Dumper;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\DumperInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Dumper\YamlDumper;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;

/**
 * YamlDumperTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class YamlDumperTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testDump(): void
    {
        self::assertSame($this->getExpected(), (new YamlDumper())->dump($this->getSchemaNode(), 'managed_schemas', SchemaConfigurationGenerator::$nodeTypes));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testremovedNode(): void
    {
        self::assertStringNotContainsString('dynamic_fields', (new YamlDumper())->dump($this->getSchemaNode(), 'managed_schemas', SchemaConfigurationGenerator::$nodeTypes));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testBeautify(): void
    {
        $readable = (new YamlDumper())->dump($this->getSchemaNode(), 'managed_schemas', SchemaConfigurationGenerator::$nodeTypes);
        $nonReadable = (new YamlDumper())->dump($this->getSchemaNode(), 'managed_schemas', SchemaConfigurationGenerator::$nodeTypes, false);

        self::assertNotSame($readable, $nonReadable);

        self::assertStringContainsString('position_increment_gap', $readable);
        self::assertStringContainsString('position_increment_gap', $nonReadable);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testDefaultValue(): void
    {
        $default = (new YamlDumper())->dump($this->getSchemaNode(), 'managed_schemas', SchemaConfigurationGenerator::$nodeTypes);
        $defined = (new YamlDumper())->dump($this->getSchemaNode(), 'managed_schemas', SchemaConfigurationGenerator::$nodeTypes, true);

        self::assertSame($default, $defined);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExtension(): void
    {
        self::assertSame(DumperInterface::EXTENSION_YAML, YamlDumper::getExtension());
    }

    /**
     * @return string
     */
    public function getExpected(): string
    {
        return <<<'YAML'
managed_schemas:
  fields:
  - { name: foo, type: bar, sort_missing_first: true }
copy_fields:
  - { source: foo, dest: bar, max_chars: 24 }
field_types:
  -
    name: foo
    class: baz
    position_increment_gap: 1
    analyzers:
      - { class: foo, type: bar, char_filters: [{ class: foo, pattern: bar, replacement: baz }], tokenizer: { class: foo, pattern: pattern }, filters: [{ class: bar, min_gram_size: 1 }] }

YAML;
    }

    /**
     * @return \array[][][]
     */
    public function getConfigNode(): array
    {
        return [
            'managed_schemas' => [
                    'fields' => [
                        [
                            'name' => 'foo',
                            'type' => 'bar',
                            'sort_missing_first' => true,
                        ],
                    ],
                ],
            ]
        ;
    }

    /**
     * @return \array[][]
     */
    public function getSchemaNode(): array
    {
        return [
            'fields' => [
                [
                    'name' => 'foo',
                    'type' => 'bar',
                    'sort_missing_first' => true,
                ],
            ],
            'copy_fields' => [
                [
                    'source' => 'foo',
                    'dest' => 'bar',
                    'max_chars' => 24,
                ],
            ],
            'dynamic_fields' => [
            ],
            'field_types' => [
                [
                    'name' => 'foo',
                    'class' => 'baz',
                    'position_increment_gap' => 1,
                    'analyzers' => [
                        [
                            'class' => 'foo',
                            'type' => 'bar',
                            'char_filters' => [
                                [
                                    'class' => 'foo',
                                    'pattern' => 'bar',
                                    'replacement' => 'baz',
                                ],
                            ],
                            'tokenizer' => [
                                'class' => 'foo',
                                'pattern' => 'pattern',
                            ],
                            'filters' => [
                                [
                                    'class' => 'bar',
                                    'min_gram_size' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
