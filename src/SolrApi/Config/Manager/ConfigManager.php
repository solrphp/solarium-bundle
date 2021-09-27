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

use JMS\Serializer\DeserializationContext;
use Solrphp\SolariumBundle\Common\Manager\AbstractApiManager;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command as ConfigCommands;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath as ConfigSubPaths;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;

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
    protected static ?string $api = null;

    /**
     * {@inheritdoc}
     */
    public function call(string $path): ResponseInterface
    {
        $response = parent::call($path);

        return $this->serializer->deserialize($response->getBody() ?? '{}', ResponseInterface::class, 'json', $this->createContext());
    }

    /**
     * the solr pre-deserialization event subscriber will modify data
     * and change type to the one set in the solrphp.real_class attribute.
     *
     * @return \JMS\Serializer\DeserializationContext
     */
    private function createContext(): DeserializationContext
    {
        return DeserializationContext::create()
            ->setAttribute('solrphp.real_class', ConfigResponse::class)
        ;
    }
}
