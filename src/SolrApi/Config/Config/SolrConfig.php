<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Config;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;

/**
 * Solr Config.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrConfig implements CoreDependentConfigInterface
{
    /**
     * @var ArrayCollection<int, string>
     */
    private ArrayCollection $cores;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent>
     */
    private ArrayCollection $searchComponents;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler>
     */
    private ArrayCollection $requestHandlers;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Query|null
     */
    private ?Query $query;

    /**
     * @param ArrayCollection<int, string>                                                            $cores
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent>|null $searchComponents
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler>|null  $requestHandlers
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Query|null                                 $query
     */
    public function __construct(ArrayCollection $cores, ArrayCollection $searchComponents = null, ArrayCollection $requestHandlers = null, Query $query = null)
    {
        $this->cores = $cores;
        $this->searchComponents = $searchComponents ?? new ArrayCollection();
        $this->requestHandlers = $requestHandlers ?? new ArrayCollection();
        $this->query = $query;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getCores(): ArrayCollection
    {
        return $this->cores;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<int, string> $cores
     */
    public function setCores(ArrayCollection $cores): void
    {
        $this->cores = $cores;
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent>
     */
    public function getSearchComponents(): ArrayCollection
    {
        return $this->searchComponents;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent $searchComponent
     */
    public function addSearchComponent(SearchComponent $searchComponent): void
    {
        $this->searchComponents->add($searchComponent);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent $searchComponent
     *
     * @return bool
     */
    public function removeSearchComponent(SearchComponent $searchComponent): bool
    {
        return $this->searchComponents->removeElement($searchComponent);
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler>
     */
    public function getRequestHandlers(): ArrayCollection
    {
        return $this->requestHandlers;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler $requestHandler
     */
    public function addRequestHandler(RequestHandler $requestHandler): void
    {
        $this->requestHandlers->add($requestHandler);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler $requestHandler
     *
     * @return bool
     */
    public function removeRequestHandler(RequestHandler $requestHandler): bool
    {
        return $this->requestHandlers->removeElement($requestHandler);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Query|null
     */
    public function getQuery(): ?Query
    {
        return $this->query;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Query|null $query
     */
    public function setQuery(?Query $query): void
    {
        $this->query = $query;
    }
}
