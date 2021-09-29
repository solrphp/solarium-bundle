<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Dumper;

use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Yaml Dumper.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class YamlDumper implements DumperInterface
{
    /**
     * @var array<string, array<int>>
     */
    private static array $dumpVars = [
        ConfigGenerator::TYPE_FIELD => [2, 2],
        ConfigGenerator::TYPE_COPY_FIELD => [2, 2],
        ConfigGenerator::TYPE_DYNAMIC_FIELD => [2, 2],
        ConfigGenerator::TYPE_FIELD_TYPE => [4, 2],
    ];

    /**
     * {@inheritdoc}
     */
    public function dump(array $config, string $rootNode, array $types): string
    {
        $output = $rootNode.':'.\PHP_EOL;

        foreach ($types as $type) {
            $output .= Yaml::dump([$type => $config[$type]], ...self::$dumpVars[$type]);
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtension(): string
    {
        return DumperInterface::EXTENSION_YAML;
    }
}
