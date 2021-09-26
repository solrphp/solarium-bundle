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
use JMS\Serializer\SerializerInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler;

/**
 * Config Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigGenerator
{
    /**
     * @var string
     */
    private string $format = 'json';

    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * constructor.
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array<int, array<string, mixed>> $configs
     *
     * @return \Generator<int, \Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface>
     */
    public function generate(array $configs): \Generator
    {
        foreach ($configs as $config) {
            foreach ($config['search_components'] as $key => $searchComponent) {
                $config['search_components'][$key] = $this->serializer->deserialize(json_encode($searchComponent, \JSON_THROW_ON_ERROR), SearchComponent::class, $this->format);
            }

            foreach ($config['request_handlers'] as $key => $requestHandler) {
                $config['request_handlers'][$key] = $this->serializer->deserialize(json_encode($requestHandler, \JSON_THROW_ON_ERROR), RequestHandler::class, $this->format);
            }

            if (false === empty($config['query'])) {
                $config['query'] = $this->serializer->deserialize(json_encode($config['query'], \JSON_THROW_ON_ERROR), Query::class, $this->format);
            } else {
                $config['query'] = null;
            }

            if (false === empty($config['update_handler'])) {
                $config['update_handler'] = $this->serializer->deserialize(json_encode($config['update_handler'], \JSON_THROW_ON_ERROR), UpdateHandler::class, $this->format);
            } else {
                $config['update_handler'] = null;
            }

            if (false === empty($config['request_dispatcher'])) {
                $config['request_dispatcher'] = $this->serializer->deserialize(json_encode($config['request_dispatcher'], \JSON_THROW_ON_ERROR), RequestDispatcher::class, $this->format);
            } else {
                $config['request_dispatcher'] = null;
            }

            yield new SolrConfig(new ArrayCollection($config['cores']), new ArrayCollection($config['search_components']), new ArrayCollection($config['request_handlers']), $config['query'], $config['update_handler'], $config['request_dispatcher']);
        }
    }
}
