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
 * N-Gram Filter.
 *
 * @author wicliff <wwolda@gmail.com>
 */
final class NGramFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.NGramFilterFactory';

    /**
     * @var int|null
     */
    private ?int $minGramSize = null;

    /**
     * @var int|null
     */
    private ?int $maxGramSize = null;

    /**
     * @var bool|null
     */
    private ?bool $preserveOriginal = null;

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
    public function getMinGramSize(): ?int
    {
        return $this->minGramSize;
    }

    /**
     * @param int|null $minGramSize
     */
    public function setMinGramSize(?int $minGramSize): void
    {
        $this->minGramSize = $minGramSize;
    }

    /**
     * @return int|null
     */
    public function getMaxGramSize(): ?int
    {
        return $this->maxGramSize;
    }

    /**
     * @param int|null $maxGramSize
     */
    public function setMaxGramSize(?int $maxGramSize): void
    {
        $this->maxGramSize = $maxGramSize;
    }

    /**
     * @return bool|null
     */
    public function getPreserveOriginal(): ?bool
    {
        return $this->preserveOriginal;
    }

    /**
     * @param bool|null $preserveOriginal
     */
    public function setPreserveOriginal(?bool $preserveOriginal): void
    {
        $this->preserveOriginal = $preserveOriginal;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'minGramSize' => $this->minGramSize,
                'maxGramSize' => $this->maxGramSize,
                'preserveOriginal' => $this->preserveOriginal,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
