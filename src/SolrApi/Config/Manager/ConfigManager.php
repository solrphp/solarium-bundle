<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Manager;

use Solrphp\SolariumBundle\Common\Manager\AbstractApiManager;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command as ConfigCommands;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath as ConfigSubPaths;

/**
 * Config Manager.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigManager extends AbstractApiManager
{
    /**
     * {@inheritdoc}
     */
    protected static array $availableCommands = ConfigCommands::ALL;

    /**
     * {@inheritdoc}
     */
    protected static array $availableSubPaths = ConfigSubPaths::ALL;

    /**
     * {@inheritdoc}
     */
    protected static string $handler = 'config';

    /**
     * {@inheritdoc}
     */
    public function call(string $path): ResponseInterface
    {
        $response = parent::call($path);

        if (false === \array_key_exists($path, ConfigSubPaths::RESPONSE_CLASSES)) {
            return $response;
        }

        return $this->serializer->deserialize($response->getBody(), ConfigSubPaths::RESPONSE_CLASSES[$path], 'json');
    }
}
