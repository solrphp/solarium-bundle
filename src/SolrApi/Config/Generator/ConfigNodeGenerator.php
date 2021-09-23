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

use Solrphp\SolariumBundle\Common\Manager\ConfigNode;
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler;

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
     * @return array<int, IterableConfigNode|ConfigNode>
     */
    public function get(SolrConfig $config): array
    {
        $return = [];

        if (\count($config->getSearchComponents())) {
            $return[] = new IterableConfigNode(SearchComponent::class, SubPath::GET_SEARCH_COMPONENTS, $config->getSearchComponents());
        }

        if (\count($config->getRequestHandlers())) {
            $return[] = new IterableConfigNode(RequestHandler::class, SubPath::GET_REQUEST_HANDLERS, $config->getRequestHandlers());
        }

        if (null !== $query = $config->getQuery()) {
            $return[] = new ConfigNode(Query::class, SubPath::GET_QUERY, $query);
        }

        if (null !== $updateHandler = $config->getUpdateHandler()) {
            $return[] = new ConfigNode(UpdateHandler::class, SubPath::GET_UPDATE_HANDLER, $updateHandler);
        }

        return $return;
    }
}
