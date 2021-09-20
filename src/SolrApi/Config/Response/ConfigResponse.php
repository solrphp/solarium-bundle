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

use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;

/**
 * Config Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig
     */
    private SolrConfig $config;

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig
     */
    public function getConfig(): SolrConfig
    {
        return $this->config;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig $config
     */
    public function setConfig(SolrConfig $config): void
    {
        $this->config = $config;
    }
}
