<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Query.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Query implements \JsonSerializable
{
    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $useFilterForSortedQuery = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $queryResultWindowSize = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $queryResultMaxDocsCached = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $enableLazyFieldLoading = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $maxBooleanClauses = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null
     *
     * @Serializer\Type("Solrphp\SolariumBundle\SolrApi\Config\Model\Cache")
     */
    private ?Cache $filterCache = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null
     *
     * @Serializer\Type("Solrphp\SolariumBundle\SolrApi\Config\Model\Cache")
     */
    private ?Cache $queryResultCache = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null
     *
     * @Serializer\Type("Solrphp\SolariumBundle\SolrApi\Config\Model\Cache")
     */
    private ?Cache $documentCache = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $useCircuitBreakers = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $memoryCircuitBreakerThresholdPct = null;

    /**
     * @return bool|null
     */
    public function getUseFilterForSortedQuery(): ?bool
    {
        return $this->useFilterForSortedQuery;
    }

    /**
     * @param bool|null $useFilterForSortedQuery
     */
    public function setUseFilterForSortedQuery(?bool $useFilterForSortedQuery): void
    {
        $this->useFilterForSortedQuery = $useFilterForSortedQuery;
    }

    /**
     * @return int|null
     */
    public function getQueryResultWindowSize(): ?int
    {
        return $this->queryResultWindowSize;
    }

    /**
     * @param int|null $queryResultWindowSize
     */
    public function setQueryResultWindowSize(?int $queryResultWindowSize): void
    {
        $this->queryResultWindowSize = $queryResultWindowSize;
    }

    /**
     * @return int|null
     */
    public function getQueryResultMaxDocsCached(): ?int
    {
        return $this->queryResultMaxDocsCached;
    }

    /**
     * @param int|null $queryResultMaxDocsCached
     */
    public function setQueryResultMaxDocsCached(?int $queryResultMaxDocsCached): void
    {
        $this->queryResultMaxDocsCached = $queryResultMaxDocsCached;
    }

    /**
     * @return bool|null
     */
    public function getEnableLazyFieldLoading(): ?bool
    {
        return $this->enableLazyFieldLoading;
    }

    /**
     * @param bool|null $enableLazyFieldLoading
     */
    public function setEnableLazyFieldLoading(?bool $enableLazyFieldLoading): void
    {
        $this->enableLazyFieldLoading = $enableLazyFieldLoading;
    }

    /**
     * @return int|null
     */
    public function getMaxBooleanClauses(): ?int
    {
        return $this->maxBooleanClauses;
    }

    /**
     * @param int|null $maxBooleanClauses
     */
    public function setMaxBooleanClauses(?int $maxBooleanClauses): void
    {
        $this->maxBooleanClauses = $maxBooleanClauses;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null
     */
    public function getFilterCache(): ?Cache
    {
        return $this->filterCache;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null $filterCache
     */
    public function setFilterCache(?Cache $filterCache): void
    {
        $this->filterCache = $filterCache;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null
     */
    public function getQueryResultCache(): ?Cache
    {
        return $this->queryResultCache;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null $queryResultCache
     */
    public function setQueryResultCache(?Cache $queryResultCache): void
    {
        $this->queryResultCache = $queryResultCache;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null
     */
    public function getDocumentCache(): ?Cache
    {
        return $this->documentCache;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Cache|null $documentCache
     */
    public function setDocumentCache(?Cache $documentCache): void
    {
        $this->documentCache = $documentCache;
    }

    /**
     * @return bool|null
     */
    public function getUseCircuitBreakers(): ?bool
    {
        return $this->useCircuitBreakers;
    }

    /**
     * @param bool|null $useCircuitBreakers
     */
    public function setUseCircuitBreakers(?bool $useCircuitBreakers): void
    {
        $this->useCircuitBreakers = $useCircuitBreakers;
    }

    /**
     * @return int|null
     */
    public function getMemoryCircuitBreakerThresholdPct(): ?int
    {
        return $this->memoryCircuitBreakerThresholdPct;
    }

    /**
     * @param int|null $memoryCircuitBreakerThresholdPct
     */
    public function setMemoryCircuitBreakerThresholdPct(?int $memoryCircuitBreakerThresholdPct): void
    {
        $this->memoryCircuitBreakerThresholdPct = $memoryCircuitBreakerThresholdPct;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'useFilterForSortedQuery' => $this->useFilterForSortedQuery,
                'queryResultWindowSize' => $this->queryResultWindowSize,
                'queryResultMaxDocsCached' => $this->queryResultMaxDocsCached,
                'enableLazyFieldLoading' => $this->enableLazyFieldLoading,
                'maxBooleanClauses' => $this->maxBooleanClauses,
                'filterCache' => $this->filterCache,
                'queryResultCache' => $this->queryResultCache,
                'documentCache' => $this->documentCache,
                'useCircuitBreakers' => $this->useCircuitBreakers,
                'memoryCircuitBreakerThresholdPct' => $this->memoryCircuitBreakerThresholdPct,
            ],
            static function ($value) {
                return null !== $value;
            }
        );
    }
}
