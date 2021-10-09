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
 * Field Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class FieldGeneratorHandler implements SchemaGeneratorHandlerInterface
{
    /**
     * @var array<string>
     */
    public static array $attributes = [
        'name',
        'type',
        'default',
        'indexed',
        'stored',
        'docValues',
        'sortMissingFirst',
        'sortMissingLast',
        'multiValued',
        'uninvertible',
        'omitNorms',
        'omitTermFreqAndPositions',
        'omitPositions',
        'termVectors',
        'termPositions',
        'termOffsets',
        'termPayloads',
        'required',
        'useDocValuesAsStored',
        'large',
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        $nodes = [];

        foreach ($crawler->filterXPath('//schema/field')->extract(self::$attributes) as $field) {
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
        return SchemaConfigurationGenerator::TYPE_FIELD === $type;
    }
}
