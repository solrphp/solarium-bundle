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
 * Word Delimiter Graph Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class WordDelimiterGraphFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.WordDelimiterGraphFilterFactory';
    /**
     * @var int|null
     */
    private ?int $generateWordParts = null;
    /**
     * @var int|null
     */
    private ?int $generateNumberParts = null;
    /**
     * @var int|null
     */
    private ?int $splitOnCaseChange = null;
    /**
     * @var int|null
     */
    private ?int $splitOnNumerics = null;
    /**
     * @var int|null
     */
    private ?int $catenateWords = null;
    /**
     * @var int|null
     */
    private ?int $catenateNumbers = null;
    /**
     * @var int|null
     */
    private ?int $catenateAll = null;
    /**
     * @var int|null
     */
    private ?int $preserveOriginal = null;
    /**
     * @var string|null
     */
    private ?string $protected = null;
    /**
     * @var int|null
     */
    private ?int $stemEnglishPossessive = null;
    /**
     * @var string|null
     */
    private ?string $types = null;

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
     * @return int|null
     */
    public function getGenerateWordParts(): ?int
    {
        return $this->generateWordParts;
    }

    /**
     * @param int|null $generateWordParts
     */
    public function setGenerateWordParts(?int $generateWordParts): void
    {
        $this->generateWordParts = $generateWordParts;
    }

    /**
     * @return int|null
     */
    public function getGenerateNumberParts(): ?int
    {
        return $this->generateNumberParts;
    }

    /**
     * @param int|null $generateNumberParts
     */
    public function setGenerateNumberParts(?int $generateNumberParts): void
    {
        $this->generateNumberParts = $generateNumberParts;
    }

    /**
     * @return int|null
     */
    public function getSplitOnCaseChange(): ?int
    {
        return $this->splitOnCaseChange;
    }

    /**
     * @param int|null $splitOnCaseChange
     */
    public function setSplitOnCaseChange(?int $splitOnCaseChange): void
    {
        $this->splitOnCaseChange = $splitOnCaseChange;
    }

    /**
     * @return int|null
     */
    public function getSplitOnNumerics(): ?int
    {
        return $this->splitOnNumerics;
    }

    /**
     * @param int|null $splitOnNumerics
     */
    public function setSplitOnNumerics(?int $splitOnNumerics): void
    {
        $this->splitOnNumerics = $splitOnNumerics;
    }

    /**
     * @return int|null
     */
    public function getCatenateWords(): ?int
    {
        return $this->catenateWords;
    }

    /**
     * @param int|null $catenateWords
     */
    public function setCatenateWords(?int $catenateWords): void
    {
        $this->catenateWords = $catenateWords;
    }

    /**
     * @return int|null
     */
    public function getCatenateNumbers(): ?int
    {
        return $this->catenateNumbers;
    }

    /**
     * @param int|null $catenateNumbers
     */
    public function setCatenateNumbers(?int $catenateNumbers): void
    {
        $this->catenateNumbers = $catenateNumbers;
    }

    /**
     * @return int|null
     */
    public function getCatenateAll(): ?int
    {
        return $this->catenateAll;
    }

    /**
     * @param int|null $catenateAll
     */
    public function setCatenateAll(?int $catenateAll): void
    {
        $this->catenateAll = $catenateAll;
    }

    /**
     * @return int|null
     */
    public function getPreserveOriginal(): ?int
    {
        return $this->preserveOriginal;
    }

    /**
     * @param int|null $preserveOriginal
     */
    public function setPreserveOriginal(?int $preserveOriginal): void
    {
        $this->preserveOriginal = $preserveOriginal;
    }

    /**
     * @return string|null
     */
    public function getProtected(): ?string
    {
        return $this->protected;
    }

    /**
     * @param string|null $protected
     */
    public function setProtected(?string $protected): void
    {
        $this->protected = $protected;
    }

    /**
     * @return int|null
     */
    public function getStemEnglishPossessive(): ?int
    {
        return $this->stemEnglishPossessive;
    }

    /**
     * @param int|null $stemEnglishPossessive
     */
    public function setStemEnglishPossessive(?int $stemEnglishPossessive): void
    {
        $this->stemEnglishPossessive = $stemEnglishPossessive;
    }

    /**
     * @return string|null
     */
    public function getTypes(): ?string
    {
        return $this->types;
    }

    /**
     * @param string|null $types
     */
    public function setTypes(?string $types): void
    {
        $this->types = $types;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'generateWordParts' => $this->generateWordParts,
                'generateNumberParts' => $this->generateNumberParts,
                'splitOnCaseChange' => $this->splitOnCaseChange,
                'splitOnNumerics' => $this->splitOnNumerics,
                'catenateWords' => $this->catenateWords,
                'catenateNumbers' => $this->catenateNumbers,
                'catenateAll' => $this->catenateAll,
                'preserveOriginal' => $this->preserveOriginal,
                'protected' => $this->protected,
                'stemEnglishPossessive' => $this->stemEnglishPossessive,
                'types' => $this->types,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
