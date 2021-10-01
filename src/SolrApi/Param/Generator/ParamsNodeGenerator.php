<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Generator;

use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;

/**
 * ParamsNode Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamsNodeGenerator
{
    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters $parameters
     *
     * @return array<IterableConfigNode>
     */
    public function get(RequestParameters $parameters): array
    {
        if (\count($parameters->getParameterSetMaps())) {
            return [new IterableConfigNode(ParameterSetMap::class, SubPath::LIST_PARAMS, $parameters->getParameterSetMaps())];
        }

        return [];
    }
}
