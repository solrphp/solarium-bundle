<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter;

use Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface;

/**
 * Hunspell Stem Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class HunspellStemFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.HunspellStemFilterFactory';

    /**
     * @var string
     */
    private string $dictionary;

    /**
     * @var string
     */
    private string $affix;

    /**
     * @var bool|null
     */
    private ?bool $ignoreCase = null;

    /**
     * @var bool|null
     */
    private ?bool $strictAffixParsing = null;

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getDictionary(): string
    {
        return $this->dictionary;
    }

    /**
     * @param string $dictionary
     */
    public function setDictionary(string $dictionary): void
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @return string
     */
    public function getAffix(): string
    {
        return $this->affix;
    }

    /**
     * @param string $affix
     */
    public function setAffix(string $affix): void
    {
        $this->affix = $affix;
    }

    /**
     * @return bool|null
     */
    public function getIgnoreCase(): ?bool
    {
        return $this->ignoreCase;
    }

    /**
     * @param bool|null $ignoreCase
     */
    public function setIgnoreCase(?bool $ignoreCase): void
    {
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * @return bool|null
     */
    public function getStrictAffixParsing(): ?bool
    {
        return $this->strictAffixParsing;
    }

    /**
     * @param bool|null $strictAffixParsing
     */
    public function setStrictAffixParsing(?bool $strictAffixParsing): void
    {
        $this->strictAffixParsing = $strictAffixParsing;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'dictionary' => $this->dictionary,
                'affix' => $this->affix,
                'ignoreCase' => $this->ignoreCase,
                'strictAffixParsing' => $this->strictAffixParsing,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
