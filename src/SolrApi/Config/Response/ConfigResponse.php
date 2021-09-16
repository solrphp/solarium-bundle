<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Response;

use Solrphp\SolariumBundle\Response\AbstractResponse;
use Solrphp\SolariumBundle\SolrApi\Config\SolrConfig;

/**
 * Config Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigResponse extends AbstractResponse
{
    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\SolrConfig
     */
    private SolrConfig $config;

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\SolrConfig
     */
    public function getConfig(): SolrConfig
    {
        return $this->config;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\SolrConfig $config
     */
    public function setConfig(SolrConfig $config): void
    {
        $this->config = $config;
    }
}
