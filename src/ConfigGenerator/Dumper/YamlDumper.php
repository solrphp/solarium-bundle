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

use Solrphp\SolariumBundle\ConfigGenerator\Contract\DumperInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ConfigConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ParamConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Symfony\Component\Yaml\Yaml;

/**
 * Yaml Dumper.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class YamlDumper implements DumperInterface
{
    /**
     * @var array<string, array<string, array<int|string, mixed>>>
     */
    private static array $dumpVars = [
        'managed_schemas' => [
            SchemaConfigurationGenerator::TYPE_FIELD => [2, 2],
            SchemaConfigurationGenerator::TYPE_COPY_FIELD => [2, 2],
            SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD => [2, 2],
            SchemaConfigurationGenerator::TYPE_FIELD_TYPE => [4, 2],
        ],
        'solr_configs' => [
            ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER => [3, 2],
            ConfigConfigurationGenerator::TYPE_QUERY => [3, 2],
            ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER => [3, 2],
            ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER => [3, 2],
            ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT => [3, 2],
        ],
        'parameters' => [
            ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP => [4, 2],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function dump(array $config, string $rootNode, array $types, bool $beautify = true): string
    {
        if (false === $beautify) {
            return Yaml::dump([$rootNode => $config]);
        }

        $output = $rootNode.':'.\PHP_EOL.'  ';

        foreach ($types as $type) {
            if (!isset(self::$dumpVars[$rootNode][$type], $config[$type]) || empty($config[$type])) {
                continue;
            }

            $output .= Yaml::dump([$type => $config[$type]], ...self::$dumpVars[$rootNode][$type]);
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
