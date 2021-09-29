<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType;

use Solrphp\SolariumBundle\Contract\ConfigGenerator\FieldTypeVisitorInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Filter FieldType Visitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FilterFieldTypeVisitor implements FieldTypeVisitorInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'name', // always keep name as first attribute for readability
        'class',
        'affix',
        'concat',
        'consumeAllTokens',
        'delimiter',
        'dictionary',
        'enablePositionIncrements',
        'encoder',
        'format',
        'id',
        'ignoreCase',
        'inject',
        'language',
        'languageSet',
        'managed',
        'max',
        'maxCodeLength',
        'maxFractionAsterisk',
        'maxGramSize',
        'maxOutputTokenSize',
        'maxPosAsterisk',
        'maxPosQuestion',
        'maxShingleSize',
        'maxStartOffset',
        'maxTokenCount',
        'min',
        'minGramSize',
        'minShingleSize',
        'minTrailing',
        'mode',
        'nameType',
        'outputUnigrams',
        'outputUnigramsIfNoShingles',
        'pattern',
        'payload',
        'preserveOriginal',
        'protected',
        'replace',
        'replacement',
        'ruleType',
        'snowball',
        'strictAffixParsing',
        'tokenSeparator',
        'typeMatch',
        'withOriginal',
        'words',
        'wordset',
        'wrappedFilters',
    ];

    /**
     * {@inheritDoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$analyzer): void
    {
        $analyzer['filters'] = [];

        foreach ($crawler->filterXPath('//filter')->extract(self::$attributes) as $filter) {
            if (false === $combined = @array_combine(self::$attributes, $filter)) {
                continue;
            }

            $analyzer['filters'][] = $closure(array_filter($combined));
        }

        if (empty($analyzer['filters'])) {
            unset($analyzer['filters']);
        }
    }
}
