<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\SolrApi;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * FilterClassInterface.
 *
 * @author wicliff <wwolda@gmail.com>
 *
 * @DiscriminatorMap(typeProperty="class", mapping={
 *  "solr.HyphenatedWordsFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\HyphenatedWordsFilter",
 *  "solr.PatternReplaceFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\PatternReplaceFilter",
 *  "solr.SynonymGraphFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\SynonymGraphFilter",
 *  "solr.SnowballPorterFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\SnowballPorterFilter",
 *  "solr.LimitTokenPositionFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\LimitTokenPositionFilter",
 *  "solr.BeiderMorseFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\BeiderMorseFilter",
 *  "solr.ReversedWildcardFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ReversedWildcardFilter",
 *  "solr.FingerprintFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\FingerprintFilter",
 *  "solr.PhoneticFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\PhoneticFilter",
 *  "solr.ASCIIFoldingFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ASCIIFoldingFilter",
 *  "solr.StopFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\StopFilter",
 *  "solr.KeepWordFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\KeepWordFilter",
 *  "solr.ManagedStopFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ManagedSynonymGraphFilter",
 *  "solr.NGramFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\NGramFilter",
 *  "solr.ICUTransformFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ICUTransformFilter",
 *  "solr.KStemFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\KStemFilter",
 *  "solr.ShingleFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ShingleFilter",
 *  "solr.CommonGramsFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\CommonGramsFilter",
 *  "solr.WordDelimiterGraphFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\WordDelimiterGraphFilter",
 *  "solr.ManagedStopFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ManagedStopFilter",
 *  "solr.LengthFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\LengthFilter",
 *  "solr.DoubleMetaphoneFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\DoubleMetaphoneFilter",
 *  "solr.TokenOffsetPayloadTokenFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\TokenOffsetPayloadTokenFilter",
 *  "solr.TrimFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\TrimFilter",
 *  "solr.EnglishMinimalStemFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\EnglishMinimalStemFilter",
 *  "solr.ICUFoldingFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ICUFoldingFilter",
 *  "solr.ICUNormalizer2FilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ICUNormalizer2Filter",
 *  "solr.DaitchMokotoffSoundexFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\DaitchMokotoffSoundexFilter",
 *  "solr.ClassicFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ClassicFilter",
 *  "solr.TypeAsSynonymFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\TypeAsSynonymFilter",
 *  "solr.EnglishPossessiveFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\EnglishPossessiveFilter",
 *  "solr.HunspellStemFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\HunspellStemFilter",
 *  "solr.PorterStemFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\PorterStemFilter",
 *  "solr.NumericPayloadTokenFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\NumericPayloadTokenFilter",
 *  "solr.LowerCaseFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\LowerCaseFilter",
 *  "solr.EdgeNGramFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\EdgeNGramFilter",
 *  "solr.DelimitedBoostTokenFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\DelimitedBoostTokenFilter",
 *  "solr.LimitTokenOffsetFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\LimitTokenOffsetFilter",
 *  "solr.TypeTokenFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\TypeTokenFilter",
 *  "solr.TypeAsPayloadTokenFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\TypeAsPayloadTokenFilter",
 *  "solr.FlattenGraphFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\FlattenGraphFilter",
 *  "solr.LimitTokenCountFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\LimitTokenCountFilter",
 *  "solr.SuggestStopFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\SuggestStopFilter",
 *  "solr.RemoveDuplicatesTokenFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\RemoveDuplicatesTokenFilter",
 *  "solr.ProtectedTermFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter\ProtectedTermFilter",
 *  "solr.ICUNormalizer2CharFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter\ICUNormalizer2CharFilter",
 *  "solr.MappingCharFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter\MappingCharFilter",
 *  "solr.PatternReplaceCharFilterFactory" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter\PatternReplaceCharFilter",
 *  "org.apache.solr.analysis.HTMLStripCharFilter" = "Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter\HTMLStripCharFilter"
 * })
 */
interface FilterInterface
{
    /**
     * @return string
     */
    public function getClass(): string;
}
