<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config;

/**
 * Query.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Query implements \JsonSerializable
{
    /**
     * @var bool|null
     */
    private ?bool $useFilterForSortedQuery = null;

    /**
     * @var int|null
     */
    private ?int $queryResultWindowSize = null;

    /**
     * @var int|null
     */
    private ?int $queryResultMaxDocsCached = null;

    /**
     * @var bool|null
     */
    private ?bool $enableLazyFieldLoading = null;

    /**
     * @var int|null
     */
    private ?int $maxBooleanClauses = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Cache
     */
    private Cache $filterCache;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Cache
     */
    private Cache $queryResultCache;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Cache
     */
    private Cache $documentCache;

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
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Cache
     */
    public function getFilterCache(): Cache
    {
        return $this->filterCache;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Cache $filterCache
     */
    public function setFilterCache(Cache $filterCache): void
    {
        $this->filterCache = $filterCache;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Cache
     */
    public function getQueryResultCache(): Cache
    {
        return $this->queryResultCache;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Cache $queryResultCache
     */
    public function setQueryResultCache(Cache $queryResultCache): void
    {
        $this->queryResultCache = $queryResultCache;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Cache
     */
    public function getDocumentCache(): Cache
    {
        return $this->documentCache;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Cache $documentCache
     */
    public function setDocumentCache(Cache $documentCache): void
    {
        $this->documentCache = $documentCache;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
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
            ],
            static function ($value) {
                return null !== $value;
            }
        );
    }
}
