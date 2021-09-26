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

use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * Shingle Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ShingleFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.ShingleFilterFactory';

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $minShingleSize = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $maxShingleSize = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $outputUnigrams = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $outputUnigramsIfNoShingles = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $tokenSeparator = null;

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
    public function getMinShingleSize(): ?int
    {
        return $this->minShingleSize;
    }

    /**
     * @param int|null $minShingleSize
     */
    public function setMinShingleSize(?int $minShingleSize): void
    {
        $this->minShingleSize = $minShingleSize;
    }

    /**
     * @return int|null
     */
    public function getMaxShingleSize(): ?int
    {
        return $this->maxShingleSize;
    }

    /**
     * @param int|null $maxShingleSize
     */
    public function setMaxShingleSize(?int $maxShingleSize): void
    {
        $this->maxShingleSize = $maxShingleSize;
    }

    /**
     * @return bool|null
     */
    public function getOutputUnigrams(): ?bool
    {
        return $this->outputUnigrams;
    }

    /**
     * @param bool|null $outputUnigrams
     */
    public function setOutputUnigrams(?bool $outputUnigrams): void
    {
        $this->outputUnigrams = $outputUnigrams;
    }

    /**
     * @return bool|null
     */
    public function getOutputUnigramsIfNoShingles(): ?bool
    {
        return $this->outputUnigramsIfNoShingles;
    }

    /**
     * @param bool|null $outputUnigramsIfNoShingles
     */
    public function setOutputUnigramsIfNoShingles(?bool $outputUnigramsIfNoShingles): void
    {
        $this->outputUnigramsIfNoShingles = $outputUnigramsIfNoShingles;
    }

    /**
     * @return string|null
     */
    public function getTokenSeparator(): ?string
    {
        return $this->tokenSeparator;
    }

    /**
     * @param string|null $tokenSeparator
     */
    public function setTokenSeparator(?string $tokenSeparator): void
    {
        $this->tokenSeparator = $tokenSeparator;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'minShingleSize' => $this->minShingleSize,
                'maxShingleSize' => $this->maxShingleSize,
                'outputUnigrams' => $this->outputUnigrams,
                'outputUnigramsIfNoShingles' => $this->outputUnigramsIfNoShingles,
                'tokenSeparator' => $this->tokenSeparator,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
