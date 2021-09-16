<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Model;

/**
 * Field Type.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class FieldType implements \JsonSerializable
{
    use FieldPropertyTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $class = null;

    /**
     * @var int|null
     */
    private ?int $positionIncrementGap = null;

    /**
     * @var bool|null
     */
    private ?bool $autoGeneratePhraseQueries = null;

    /**
     * @var string|null
     */
    private ?string $synonymQueryStyle = null;

    /**
     * @var bool|null
     */
    private ?bool $enableGraphQueries = null;

    /**
     * @var string|null
     */
    private ?string $docValuesFormat = null;

    /**
     * @var string|null
     */
    private ?string $postingsFormat = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Model\Analyzer[]
     */
    private array $analyzers = [];

    /**
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string|null $class
     */
    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return int|null
     */
    public function getPositionIncrementGap(): ?int
    {
        return $this->positionIncrementGap;
    }

    /**
     * @param int|null $positionIncrementGap
     */
    public function setPositionIncrementGap(?int $positionIncrementGap): void
    {
        $this->positionIncrementGap = $positionIncrementGap;
    }

    /**
     * @return bool|null
     */
    public function getAutoGeneratePhraseQueries(): ?bool
    {
        return $this->autoGeneratePhraseQueries;
    }

    /**
     * @param bool|null $autoGeneratePhraseQueries
     */
    public function setAutoGeneratePhraseQueries(?bool $autoGeneratePhraseQueries): void
    {
        $this->autoGeneratePhraseQueries = $autoGeneratePhraseQueries;
    }

    /**
     * @return string|null
     */
    public function getSynonymQueryStyle(): ?string
    {
        return $this->synonymQueryStyle;
    }

    /**
     * @param string|null $synonymQueryStyle
     */
    public function setSynonymQueryStyle(?string $synonymQueryStyle): void
    {
        $this->synonymQueryStyle = $synonymQueryStyle;
    }

    /**
     * @return bool|null
     */
    public function getEnableGraphQueries(): ?bool
    {
        return $this->enableGraphQueries;
    }

    /**
     * @param bool|null $enableGraphQueries
     */
    public function setEnableGraphQueries(?bool $enableGraphQueries): void
    {
        $this->enableGraphQueries = $enableGraphQueries;
    }

    /**
     * @return string|null
     */
    public function getDocValuesFormat(): ?string
    {
        return $this->docValuesFormat;
    }

    /**
     * @param string|null $docValuesFormat
     */
    public function setDocValuesFormat(?string $docValuesFormat): void
    {
        $this->docValuesFormat = $docValuesFormat;
    }

    /**
     * @return string|null
     */
    public function getPostingsFormat(): ?string
    {
        return $this->postingsFormat;
    }

    /**
     * @param string|null $postingsFormat
     */
    public function setPostingsFormat(?string $postingsFormat): void
    {
        $this->postingsFormat = $postingsFormat;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Schema\Model\Analyzer[]
     */
    public function getAnalyzers(): array
    {
        return $this->analyzers;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Analyzer $analyzer
     */
    public function addAnalyzer(Analyzer $analyzer): void
    {
        $this->analyzers[] = $analyzer;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Analyzer $analyzer
     */
    public function removeAnalyzer(Analyzer $analyzer): void
    {
        if (false === $key = array_search($analyzer, $this->analyzers, true)) {
            return;
        }

        unset($this->analyzers[$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'name' => $this->name,
                'class' => $this->class,
                'positionIncrementGap' => $this->positionIncrementGap,
                'autoGeneratePhraseQueries' => $this->autoGeneratePhraseQueries,
                'synonymQueryStyle' => $this->synonymQueryStyle,
                'enableGraphQueries' => $this->enableGraphQueries,
                'docValuesFormat' => $this->docValuesFormat,
                'postingsFormat' => $this->postingsFormat,
                'analyzers' => $this->analyzers,
                'indexed' => $this->indexed,
                'stored' => $this->stored,
                'docValues' => $this->docValues,
                'sortMissingFirst' => $this->sortMissingFirst,
                'sortMissingLast' => $this->sortMissingLast,
                'multiValued' => $this->multiValued,
                'uninvertible' => $this->uninvertible,
                'omitNorms' => $this->omitNorms,
                'omitTermFreqAndPositions' => $this->omitTermFreqAndPositions,
                'omitPositions' => $this->omitPositions,
                'termVectors' => $this->termVectors,
                'termPositions' => $this->termPositions,
                'termOffsets' => $this->termOffsets,
                'termPayloads' => $this->termPayloads,
                'required' => $this->required,
                'useDocValuesAsStored' => $this->useDocValuesAsStored,
                'large' => $this->large,
            ],
            static function ($val) {
                return null !== $val && (false === \is_array($val) || 0 !== \count($val));
            }
        );
    }
}
