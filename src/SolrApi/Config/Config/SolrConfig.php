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
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler;

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
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler|null
     */
    private ?UpdateHandler $updateHandler;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher|null
     */
    private ?RequestDispatcher $requestDispatcher;

    /**
     * @param ArrayCollection<int, string>                                                            $cores
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent>|null $searchComponents
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler>|null  $requestHandlers
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Query|null                                 $query
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler|null                         $updateHandler
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher|null                     $requestDispatcher
     */
    public function __construct(ArrayCollection $cores, ArrayCollection $searchComponents = null, ArrayCollection $requestHandlers = null, Query $query = null, UpdateHandler $updateHandler = null, RequestDispatcher $requestDispatcher = null)
    {
        $this->cores = $cores;
        $this->searchComponents = $searchComponents ?? new ArrayCollection();
        $this->requestHandlers = $requestHandlers ?? new ArrayCollection();
        $this->query = $query;
        $this->updateHandler = $updateHandler;
        $this->requestDispatcher = $requestDispatcher;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getCores(): ArrayCollection
    {
        return $this->cores;
    }

    /**
     * @param string $core
     */
    public function addCore(string $core): void
    {
        $this->cores->add($core);
    }

    /**
     * @param string $core
     *
     * @return bool
     */
    public function removeCore(string $core): bool
    {
        return $this->cores->removeElement($core);
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

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler|null
     */
    public function getUpdateHandler(): ?UpdateHandler
    {
        return $this->updateHandler;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler|null $updateHandler
     */
    public function setUpdateHandler(?UpdateHandler $updateHandler): void
    {
        $this->updateHandler = $updateHandler;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher|null
     */
    public function getRequestDispatcher(): ?RequestDispatcher
    {
        return $this->requestDispatcher;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher|null $requestDispatcher
     */
    public function setRequestDispatcher(?RequestDispatcher $requestDispatcher): void
    {
        $this->requestDispatcher = $requestDispatcher;
    }
}
