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
 * Limit Token Count Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class LimitTokenCountFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.LimitTokenCountFilterFactory';

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $maxTokenCount;

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
    public function getMaxTokenCount(): int
    {
        return $this->maxTokenCount;
    }

    /**
     * @param int $maxTokenCount
     */
    public function setMaxTokenCount(int $maxTokenCount): void
    {
        $this->maxTokenCount = $maxTokenCount;
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
                'maxTokenCount' => $this->maxTokenCount,
                'consumeAllTokens' => $this->consumeAllTokens,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
