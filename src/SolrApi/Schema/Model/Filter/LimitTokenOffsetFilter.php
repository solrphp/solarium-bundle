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
 * Limit Token Offset Filter.
 *
 * @author wicliff <wwolda@gmail.com>
 */
final class LimitTokenOffsetFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.LimitTokenOffsetFilterFactory';
    /**
     * @var int
     */
    private int $maxStartOffset;
    /**
     * @var bool|null
     */
    private ?bool $consumeAllTokens = null;

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
     * @return int
     */
    public function getMaxStartOffset(): int
    {
        return $this->maxStartOffset;
    }

    /**
     * @param int $maxStartOffset
     */
    public function setMaxStartOffset(int $maxStartOffset): void
    {
        $this->maxStartOffset = $maxStartOffset;
    }

    /**
     * @return bool|null
     */
    public function getConsumeAllTokens(): ?bool
    {
        return $this->consumeAllTokens;
    }

    /**
     * @param bool|null $consumeAllTokens
     */
    public function setConsumeAllTokens(?bool $consumeAllTokens): void
    {
        $this->consumeAllTokens = $consumeAllTokens;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'maxStartOffset' => $this->maxStartOffset,
                'consumeAllTokens' => $this->consumeAllTokens,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
