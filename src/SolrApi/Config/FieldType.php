<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config;

/**
 * Field Type.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class FieldType implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $type;

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
     * @var bool|null
     */
    private ?bool $indexed = null;

    /**
     * @var bool|null
     */
    private ?bool $stored = null;

    /**
     * @var bool|null
     */
    private ?bool $docValues = null;

    /**
     * @var bool|null
     */
    private ?bool $sortMissingFirst = null;

    /**
     * @var bool|null
     */
    private ?bool $sortMissingLast = null;

    /**
     * @var bool|null
     */
    private ?bool $multiValued = null;

    /**
     * @var bool|null
     */
    private ?bool $uninvertible = null;

    /**
     * @var bool|null
     */
    private ?bool $omitNorms = null;

    /**
     * @var bool|null
     */
    private ?bool $omitTermFreqAndPositions = null;

    /**
     * @var bool|null
     */
    private ?bool $omitPositions = null;

    /**
     * @var bool|null
     */
    private ?bool $termVectors = null;

    /**
     * @var bool|null
     */
    private ?bool $termPositions = null;

    /**
     * @var bool|null
     */
    private ?bool $termOffsets = null;

    /**
     * @var bool|null
     */
    private ?bool $termPayloads = null;

    /**
     * @var bool|null
     */
    private ?bool $required = null;

    /**
     * @var bool|null
     */
    private ?bool $useDocValuesAsStored = null;

    /**
     * @var bool|null
     */
    private ?bool $large = null;

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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
     * @return bool|null
     */
    public function getIndexed(): ?bool
    {
        return $this->indexed;
    }

    /**
     * @param bool|null $indexed
     */
    public function setIndexed(?bool $indexed): void
    {
        $this->indexed = $indexed;
    }

    /**
     * @return bool|null
     */
    public function getStored(): ?bool
    {
        return $this->stored;
    }

    /**
     * @param bool|null $stored
     */
    public function setStored(?bool $stored): void
    {
        $this->stored = $stored;
    }

    /**
     * @return bool|null
     */
    public function getDocValues(): ?bool
    {
        return $this->docValues;
    }

    /**
     * @param bool|null $docValues
     */
    public function setDocValues(?bool $docValues): void
    {
        $this->docValues = $docValues;
    }

    /**
     * @return bool|null
     */
    public function getSortMissingFirst(): ?bool
    {
        return $this->sortMissingFirst;
    }

    /**
     * @param bool|null $sortMissingFirst
     */
    public function setSortMissingFirst(?bool $sortMissingFirst): void
    {
        $this->sortMissingFirst = $sortMissingFirst;
    }

    /**
     * @return bool|null
     */
    public function getSortMissingLast(): ?bool
    {
        return $this->sortMissingLast;
    }

    /**
     * @param bool|null $sortMissingLast
     */
    public function setSortMissingLast(?bool $sortMissingLast): void
    {
        $this->sortMissingLast = $sortMissingLast;
    }

    /**
     * @return bool|null
     */
    public function getMultiValued(): ?bool
    {
        return $this->multiValued;
    }

    /**
     * @param bool|null $multiValued
     */
    public function setMultiValued(?bool $multiValued): void
    {
        $this->multiValued = $multiValued;
    }

    /**
     * @return bool|null
     */
    public function getUninvertible(): ?bool
    {
        return $this->uninvertible;
    }

    /**
     * @param bool|null $uninvertible
     */
    public function setUninvertible(?bool $uninvertible): void
    {
        $this->uninvertible = $uninvertible;
    }

    /**
     * @return bool|null
     */
    public function getOmitNorms(): ?bool
    {
        return $this->omitNorms;
    }

    /**
     * @param bool|null $omitNorms
     */
    public function setOmitNorms(?bool $omitNorms): void
    {
        $this->omitNorms = $omitNorms;
    }

    /**
     * @return bool|null
     */
    public function getOmitTermFreqAndPositions(): ?bool
    {
        return $this->omitTermFreqAndPositions;
    }

    /**
     * @param bool|null $omitTermFreqAndPositions
     */
    public function setOmitTermFreqAndPositions(?bool $omitTermFreqAndPositions): void
    {
        $this->omitTermFreqAndPositions = $omitTermFreqAndPositions;
    }

    /**
     * @return bool|null
     */
    public function getOmitPositions(): ?bool
    {
        return $this->omitPositions;
    }

    /**
     * @param bool|null $omitPositions
     */
    public function setOmitPositions(?bool $omitPositions): void
    {
        $this->omitPositions = $omitPositions;
    }

    /**
     * @return bool|null
     */
    public function getTermVectors(): ?bool
    {
        return $this->termVectors;
    }

    /**
     * @param bool|null $termVectors
     */
    public function setTermVectors(?bool $termVectors): void
    {
        $this->termVectors = $termVectors;
    }

    /**
     * @return bool|null
     */
    public function getTermPositions(): ?bool
    {
        return $this->termPositions;
    }

    /**
     * @param bool|null $termPositions
     */
    public function setTermPositions(?bool $termPositions): void
    {
        $this->termPositions = $termPositions;
    }

    /**
     * @return bool|null
     */
    public function getTermOffsets(): ?bool
    {
        return $this->termOffsets;
    }

    /**
     * @param bool|null $termOffsets
     */
    public function setTermOffsets(?bool $termOffsets): void
    {
        $this->termOffsets = $termOffsets;
    }

    /**
     * @return bool|null
     */
    public function getTermPayloads(): ?bool
    {
        return $this->termPayloads;
    }

    /**
     * @param bool|null $termPayloads
     */
    public function setTermPayloads(?bool $termPayloads): void
    {
        $this->termPayloads = $termPayloads;
    }

    /**
     * @return bool|null
     */
    public function getRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @param bool|null $required
     */
    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @return bool|null
     */
    public function getUseDocValuesAsStored(): ?bool
    {
        return $this->useDocValuesAsStored;
    }

    /**
     * @param bool|null $useDocValuesAsStored
     */
    public function setUseDocValuesAsStored(?bool $useDocValuesAsStored): void
    {
        $this->useDocValuesAsStored = $useDocValuesAsStored;
    }

    /**
     * @return bool|null
     */
    public function getLarge(): ?bool
    {
        return $this->large;
    }

    /**
     * @param bool|null $large
     */
    public function setLarge(?bool $large): void
    {
        $this->large = $large;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'name' => $this->name,
                'type' => $this->type,
                'class' => $this->class,
                'positionIncrementGap' => $this->positionIncrementGap,
                'autoGeneratePhraseQueries' => $this->autoGeneratePhraseQueries,
                'synonymQueryStyle' => $this->synonymQueryStyle,
                'enableGraphQueries' => $this->enableGraphQueries,
                'docValuesFormat' => $this->docValuesFormat,
                'postingsFormat' => $this->postingsFormat,
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
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
