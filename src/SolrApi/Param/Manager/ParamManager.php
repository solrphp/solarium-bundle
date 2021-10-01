<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Manager;

use JMS\Serializer\DeserializationContext;
use Solrphp\SolariumBundle\Common\Manager\AbstractApiManager;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\Command as ParamCommands;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\SubPath as ParamSubPaths;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;

/**
 * Param Manager.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamManager extends AbstractApiManager
{
    /**
     * {@inheritdoc}
     */
    protected static array $availableCommands = ParamCommands::ALL;

    /**
     * {@inheritdoc}
     */
    protected static array $availableSubPaths = ParamSubPaths::ALL;

    /**
     * {@inheritdoc}
     */
    protected static string $handler = 'config/params';

    /**
     * {@inheritdoc}
     */
    protected static ?string $api = null;

    /**
     * {@inheritdoc}
     */
    protected static bool $isObjectApi = true;

    /**
     * {@inheritdoc}
     */
    public function call(string $path): ResponseInterface
    {
        $response = parent::call($path);

        return $this->serializer->deserialize($response->getBody() ?? '{}', ResponseInterface::class, 'json', $this->createContext());
    }

    /**
     * @return \JMS\Serializer\DeserializationContext
     */
    private function createContext(): DeserializationContext
    {
        return DeserializationContext::create()
            ->setAttribute('solrphp.real_class', ParamResponse::class)
        ;
    }
}
