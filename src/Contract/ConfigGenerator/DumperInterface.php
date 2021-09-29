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
     * @param array<string, array<int, array<string, array<int, mixed>|string>|null>> $config
     * @param string                                                                  $rootNode
     * @param array<string>                                                           $types
     *
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function dump(array $config, string $rootNode, array $types): string;

    /**
     * @return string
     */
    public static function getExtension(): string;
}
