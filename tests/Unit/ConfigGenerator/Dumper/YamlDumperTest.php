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
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Dumper\YamlDumper;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface;
use Symfony\Component\Yaml\Yaml;

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
        self::assertSame($this->getExpected(), (new YamlDumper())->dump($this->getSchemaNode(), 'solrphp_solarium', ConfigGenerator::SCHEMA_TYPES));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testBeautify(): void
    {
        $readable = (new YamlDumper())->dump($this->getSchemaNode(), 'solrphp_solarium', ConfigGenerator::SCHEMA_TYPES);
        $nonReadable = (new YamlDumper())->dump($this->getSchemaNode(), 'solrphp_solarium', ConfigGenerator::SCHEMA_TYPES, false);

        self::assertNotSame($readable, $nonReadable);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testDefaultValue(): void
    {
        $default = (new YamlDumper())->dump($this->getSchemaNode(), 'solrphp_solarium', ConfigGenerator::SCHEMA_TYPES);
        $defined = (new YamlDumper())->dump($this->getSchemaNode(), 'solrphp_solarium', ConfigGenerator::SCHEMA_TYPES, true);

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
     * this test is borked and provided to please infection.
     * note that it's not likeable that more config nodes are provided
     * than are defined in the $types property passsed to the dumper.
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function testUndefinedType(): void
    {
        $config = Yaml::parse((new YamlDumper())->dump($this->getConfigNode(), 'solrphp_solarium', array_merge(ConfigGenerator::CONFIG_TYPES, [ConfigGenerator::TYPE_FIELD])));

        self::assertArrayNotHasKey('solr_configs', $config);
        self::assertArrayNotHasKey('solr_configs', $config['solrphp_solarium']);
        self::assertArrayHasKey('managed_schemas', $config['solrphp_solarium']);
        self::assertArrayHasKey('fields', $config['solrphp_solarium']);
    }

    /**
     * @return string
     */
    public function getExpected(): string
    {
        return <<<'YAML'
solrphp_solarium:
  managed_schemas:
  fields:
  - { name: foo, type: bar, sort_missing_first: true }
dynamic_fields:
  - { name: '*_foo', type: bar, omit_norms: true }
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
            'managed_schemas' => [
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
                    [
                        'name' => '*_foo',
                        'type' => 'bar',
                        'omit_norms' => true,
                    ],
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
            ],
        ];
    }
}
