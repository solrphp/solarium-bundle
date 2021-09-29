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
        self::assertSame($this->getExpected(), (new YamlDumper())->dump($this->getConfig(), 'solrphp_solarium', ConfigGenerator::TYPES));
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
solrphp_solarium:
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
     * @return \array[][]
     */
    public function getConfig(): array
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
        ];
    }
}
