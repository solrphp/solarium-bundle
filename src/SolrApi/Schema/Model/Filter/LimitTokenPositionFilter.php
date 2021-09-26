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
 * Limit Token Position Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class LimitTokenPositionFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.LimitTokenPositionFilterFactory';

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $maxTokenPosition;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
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
    public function getMaxTokenPosition(): int
    {
        return $this->maxTokenPosition;
    }

    /**
     * @param int $maxTokenPosition
     */
    public function setMaxTokenPosition(int $maxTokenPosition): void
    {
        $this->maxTokenPosition = $maxTokenPosition;
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
                'maxTokenPosition' => $this->maxTokenPosition,
                'consumeAllTokens' => $this->consumeAllTokens,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
