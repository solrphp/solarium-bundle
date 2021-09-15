<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\SolrApi\Config\Query;
use Solrphp\SolariumBundle\SolrApi\Config\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\SolrConfig;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Config Generator.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class ConfigGenerator
{
    /**
     * @param array<int, array> $configs
     *
     * @return \Generator<int, \Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface>
     */
    public function generate(array $configs): \Generator
    {
        $serializer = new Serializer([new ArrayDenormalizer(), new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter(), null, new ReflectionExtractor())]);

        foreach ($configs as $config) {
            foreach ($config['search_components'] as $key => $searchComponent) {
                $config['search_components'][$key] = $serializer->denormalize($searchComponent, SearchComponent::class);
            }

            foreach ($config['request_handlers'] as $key => $requestHandler) {
                $config['request_handlers'][$key] = $serializer->denormalize($requestHandler, RequestHandler::class);
            }

            if (false === empty($config['query'])) {
                $config['query'] = $serializer->denormalize($config['query'], Query::class);
            } else {
                $config['query'] = null;
            }

            yield new SolrConfig(new ArrayCollection($config['cores']), new ArrayCollection($config['search_components']), new ArrayCollection($config['request_handlers']), $config['query']);
        }
    }
}
