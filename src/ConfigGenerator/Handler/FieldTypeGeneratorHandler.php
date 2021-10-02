<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler;

use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\CharFilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\FilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\TokenizerFieldTypeVisitor;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * FieldType Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldTypeGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'name', // always keep name as first attribute for readability
        'autoGeneratePhraseQueries',
        'class',
        'default',
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
     * @var array|string[]
     */
    private static array $analyzerAttributes = [
        'type',
    ];

    /**
     * @var iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>
     */
    private iterable $visitors;

    /**
     * @param iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>|null $visitors
     */
    public function __construct(iterable $visitors = null)
    {
        $this->visitors = $visitors ?? $this->getDefaultVisitors();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        $nodes = [];

        $crawler->filterXPath('//schema/fieldType')->each(function (Crawler $crawler) use ($closure, &$nodes) {
            // this inspection is disabled as the crawler's extract method will not return another array as the one fed
            /* @infection-ignore-all */
            if (false === $combined = @array_combine(self::$attributes, $crawler->extract(self::$attributes)[0])) {
                return;
            }

            $node = $closure(array_filter($combined));

            $crawler->filterXPath('//analyzer')->each(function (Crawler $crawler) use ($closure, &$node) {
                // this inspection is disabled as the crawler's extract method will not return another array as the one fed
                /* @infection-ignore-all */
                if (false === $combined = @array_combine(self::$analyzerAttributes, $crawler->extract(self::$analyzerAttributes))) {
                    return;
                }

                $analyzer = $closure(array_filter($combined));

                foreach ($this->visitors as $visitor) {
                    $visitor->visit($crawler, $closure, $analyzer);
                }

                $node['analyzers'][] = $analyzer;
            });

            $nodes[] = $node;
        });

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return ConfigGenerator::TYPE_FIELD_TYPE === $type;
    }

    /**
     * @return iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>
     */
    private function getDefaultVisitors(): iterable
    {
        return [
            new CharFilterFieldTypeVisitor(),
            new FilterFieldTypeVisitor(),
            new TokenizerFieldTypeVisitor(),
        ];
    }
}
