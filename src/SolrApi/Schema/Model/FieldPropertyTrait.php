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

use JMS\Serializer\Annotation as Serializer;

/**
 * Field Property Trait.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
trait FieldPropertyTrait
{
    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $indexed = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $stored = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $docValues = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $sortMissingFirst = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $sortMissingLast = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $multiValued = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $uninvertible = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $omitNorms = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $omitTermFreqAndPositions = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $omitPositions = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $termVectors = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $termPositions = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $termOffsets = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $termPayloads = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $required = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $useDocValuesAsStored = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $large = null;

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
}
