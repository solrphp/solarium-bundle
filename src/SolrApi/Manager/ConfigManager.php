<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Manager;

use Solrphp\SolariumBundle\SolrApi\Enum\Command\Config as ConfigCommands;
use Solrphp\SolariumBundle\SolrApi\Enum\SubPath\Config as ConfigSubPaths;

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
    protected static array $availableCommands = ConfigCommands::COMMANDS;

    /**
     * {@inheritdoc}
     */
    protected static array $availableSubPaths = ConfigSubPaths::SUB_PATHS;

    /**
     * {@inheritdoc}
     */
    protected static string $handler = 'config';

    /**
     * @param string $subPath
     *
     * @return \Solrphp\SolariumBundle\SolrApi\Response\AbstractResponse|\Solarium\Core\Client\Response
     */
    public function call(string $subPath)
    {
        $response = parent::call($subPath);

        if (false === \array_key_exists($subPath, ConfigSubPaths::RESPONSE_CLASSES)) {
            return $response;
        }

        return $this->serializer->deserialize($response->getBody(), ConfigSubPaths::RESPONSE_CLASSES[$subPath], 'json');
    }
}
