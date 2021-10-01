<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Config;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;

/**
 * Request Parameters config.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class RequestParameters implements CoreDependentConfigInterface
{
    /**
     * @var ArrayCollection<string>
     */
    private ArrayCollection $cores;

    /**
     * @var ArrayCollection<\Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap>
     */
    private ArrayCollection $parameterSetMaps;

    /**
     * @param array<string>|null                                                      $cores
     * @param array<\Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap>|null $parameterSetMaps
     */
    public function __construct(array $cores = null, array $parameterSetMaps = null)
    {
        $this->cores = new ArrayCollection($cores ?? []);
        $this->parameterSetMaps = new ArrayCollection($parameterSetMaps ?? []);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<string>
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
     * @return ArrayCollection<\Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap>
     */
    public function getParameterSetMaps(): ArrayCollection
    {
        return $this->parameterSetMaps;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap $params
     */
    public function addParameterSetMap(ParameterSetMap $params): void
    {
        $this->parameterSetMaps->add($params);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap $params
     *
     * @return bool
     */
    public function removeParameterSetMap(ParameterSetMap $params): bool
    {
        return $this->parameterSetMaps->removeElement($params);
    }
}
