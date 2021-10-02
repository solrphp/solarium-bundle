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
     * @var array<string, array<string, array<int|string, mixed>>>
     */
    private static array $dumpVars = [
        'managed_schemas' => [
            ConfigGenerator::TYPE_FIELD => [2, 2],
            ConfigGenerator::TYPE_COPY_FIELD => [2, 2],
            ConfigGenerator::TYPE_DYNAMIC_FIELD => [2, 2],
            ConfigGenerator::TYPE_FIELD_TYPE => [4, 2],
        ],
        'solr_configs' => [
            ConfigGenerator::TYPE_UPDATE_HANDLER => [2, 2],
            ConfigGenerator::TYPE_QUERY => [2, 2],
            ConfigGenerator::TYPE_REQUEST_DISPATCHER => [2, 2],
            ConfigGenerator::TYPE_REQUEST_HANDLER => [2, 2],
            ConfigGenerator::TYPE_SEARCH_COMPONENT => [2, 2],
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

        foreach ($config as $configNode => $configData) {
            $output .= $configNode.':'.\PHP_EOL.'  ';

            foreach ($types as $type) {
                if (!isset(self::$dumpVars[$configNode][$type], $configData[$type])) {
                    continue;
                }

                $output .= Yaml::dump([$type => $configData[$type]], ...self::$dumpVars[$configNode][$type]);
            }
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
