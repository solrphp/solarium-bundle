<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Schema;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\SchemaGeneratorHandlerInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Symfony\Component\DomCrawler\Crawler;

/**
 * DynamicField Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class DynamicFieldGeneratorHandler implements SchemaGeneratorHandlerInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'name',
        'autoGeneratePhraseQueries',
        'class',
        'docValues',
        'docValuesFormat',
        'enableGraphQueries',
        'indexed',
        'large',
        'multiValued',
        'omitNorms',
        'omitPositions',
        'omitTermFreqAndPositions',
        'positionIncrementGap',
        'postingsFormat',
        'required',
        'sortMissingFirst',
        'sortMissingLast',
        'stored',
        'synonymQueryStyle',
        'termOffsets',
        'termPayloads',
        'termPositions',
        'termVectors',
        'type',
        'uninvertible',
        'useDocValuesAsStored',
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        $nodes = [];

        foreach ($crawler->filterXPath('//schema/dynamicField')->extract(self::$attributes) as $field) {
            if (false === $combined = @array_combine(self::$attributes, $field)) {
                continue;
            }

            $nodes[] = $closure(array_filter($combined));
        }

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD === $type;
    }
}
