<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\ConfigGenerator;

/**
 * Dumper interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface DumperInterface
{
    public const EXTENSION_YAML = 'yaml';
    public const EXTENSION_PHP = 'php';

    public const EXTENSIONS = [
        self::EXTENSION_YAML,
        self::EXTENSION_PHP,
    ];

    /**
     * @param array<string, array<mixed>> $config
     * @param string                      $rootNode
     * @param array<string>               $types
     * @param bool                        $beautify
     *
     * @return string
     *
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function dump(array $config, string $rootNode, array $types, bool $beautify = true): string;

    /**
     * @return string
     */
    public static function getExtension(): string;
}
