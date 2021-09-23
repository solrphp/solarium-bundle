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

use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * Numeric Payload Token Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class NumericPayloadTokenFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.NumericPayloadTokenFilterFactory';

    /**
     * @var float
     */
    private float $payload;

    /**
     * @var string
     */
    private string $typeMatch;

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
     * @return float
     */
    public function getPayload(): float
    {
        return $this->payload;
    }

    /**
     * @param float $payload
     */
    public function setPayload(float $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getTypeMatch(): string
    {
        return $this->typeMatch;
    }

    /**
     * @param string $typeMatch
     */
    public function setTypeMatch(string $typeMatch): void
    {
        $this->typeMatch = $typeMatch;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => $this->class,
            'payload' => $this->payload,
            'typeMatch' => $this->typeMatch,
        ];
    }
}
