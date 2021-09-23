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

use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * Synonym Graph Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SynonymGraphFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.SynonymGraphFilterFactory';

    /**
     * @var bool|null
     */
    private ?bool $ignoreCase = null;

    /**
     * @var bool|null
     */
    private ?bool $expand = null;

    /**
     * @var string|null
     */
    private ?string $format = null;

    /**
     * @var string|null
     */
    private ?string $tokenizerFactory = null;

    /**
     * @var string|null
     */
    private ?string $analyzer = null;

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
    public function getExpand(): ?bool
    {
        return $this->expand;
    }

    /**
     * @param bool|null $expand
     */
    public function setExpand(?bool $expand): void
    {
        $this->expand = $expand;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string|null $format
     */
    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return string|null
     */
    public function getTokenizerFactory(): ?string
    {
        return $this->tokenizerFactory;
    }

    /**
     * @param string|null $tokenizerFactory
     */
    public function setTokenizerFactory(?string $tokenizerFactory): void
    {
        $this->tokenizerFactory = $tokenizerFactory;
    }

    /**
     * @return string|null
     */
    public function getAnalyzer(): ?string
    {
        return $this->analyzer;
    }

    /**
     * @param string|null $analyzer
     */
    public function setAnalyzer(?string $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'ignoreCase' => $this->ignoreCase,
                'expand' => $this->expand,
                'format' => $this->format,
                'tokenizerFactory' => $this->tokenizerFactory,
                'analyzer' => $this->analyzer,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
