<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\Common\Manager\ConfigNode;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;

/**
 * Config NodeGenerator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigNodeGenerator
{
    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig $config
     *
     * @return array<int, ConfigNode>
     */
    public function get(SolrConfig $config): array
    {
        $return = [];

        if (\count($config->getSearchComponents())) {
            $return[] = new ConfigNode(SearchComponent::class, SubPath::GET_SEARCH_COMPONENTS, $config->getSearchComponents());
        }

        if (\count($config->getRequestHandlers())) {
            $return[] = new ConfigNode(RequestHandler::class, SubPath::GET_REQUEST_HANDLERS, $config->getRequestHandlers());
        }

        if (null !== $config->getQuery()) {
            $return[] = new ConfigNode(Query::class, SubPath::GET_QUERY, new ArrayCollection([$config->getQuery()]));
        }

        return $return;
    }
}
