<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Response;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;
use Solrphp\SolariumBundle\SolrApi\Param\Model\Response;

/**
 * Param Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @var ArrayCollection<\Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap>
     *
     * @Serializer\Type("ArrayCollection<Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap>")
     */
    private ArrayCollection $params;

    /**
     * init params.
     */
    public function __construct()
    {
        $this->params = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<\Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap>
     */
    public function getParams(): ArrayCollection
    {
        return $this->params;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap $parameterSetMap
     */
    public function addParam(ParameterSetMap $parameterSetMap): void
    {
        $this->params->add($parameterSetMap);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap $parameterSetMap
     *
     * @return bool
     */
    public function removeParam(ParameterSetMap $parameterSetMap): bool
    {
        return $this->params->removeElement($parameterSetMap);
    }
}
