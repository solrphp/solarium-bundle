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
 * Reversed Wildcard Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ReversedWildcardFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.ReversedWildcardFilterFactory';

    /**
     * @var bool|null
     */
    private ?bool $withOriginal = null;

    /**
     * @var int|null
     */
    private ?int $maxPosAsterisk = null;

    /**
     * @var int|null
     */
    private ?int $maxPosQuestion = null;

    /**
     * @var float|null
     */
    private ?float $maxFractionAsterisk = null;

    /**
     * @var int|null
     */
    private ?int $minTrailing = null;

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
    public function getWithOriginal(): ?bool
    {
        return $this->withOriginal;
    }

    /**
     * @param bool|null $withOriginal
     */
    public function setWithOriginal(?bool $withOriginal): void
    {
        $this->withOriginal = $withOriginal;
    }

    /**
     * @return int|null
     */
    public function getMaxPosAsterisk(): ?int
    {
        return $this->maxPosAsterisk;
    }

    /**
     * @param int|null $maxPosAsterisk
     */
    public function setMaxPosAsterisk(?int $maxPosAsterisk): void
    {
        $this->maxPosAsterisk = $maxPosAsterisk;
    }

    /**
     * @return int|null
     */
    public function getMaxPosQuestion(): ?int
    {
        return $this->maxPosQuestion;
    }

    /**
     * @param int|null $maxPosQuestion
     */
    public function setMaxPosQuestion(?int $maxPosQuestion): void
    {
        $this->maxPosQuestion = $maxPosQuestion;
    }

    /**
     * @return float|null
     */
    public function getMaxFractionAsterisk(): ?float
    {
        return $this->maxFractionAsterisk;
    }

    /**
     * @param float|null $maxFractionAsterisk
     */
    public function setMaxFractionAsterisk(?float $maxFractionAsterisk): void
    {
        $this->maxFractionAsterisk = $maxFractionAsterisk;
    }

    /**
     * @return int|null
     */
    public function getMinTrailing(): ?int
    {
        return $this->minTrailing;
    }

    /**
     * @param int|null $minTrailing
     */
    public function setMinTrailing(?int $minTrailing): void
    {
        $this->minTrailing = $minTrailing;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'withOriginal' => $this->withOriginal,
                'maxPosAsterisk' => $this->maxPosAsterisk,
                'maxPosQuestion' => $this->maxPosQuestion,
                'maxFractionAsterisk' => $this->maxFractionAsterisk,
                'minTrailing' => $this->minTrailing,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
