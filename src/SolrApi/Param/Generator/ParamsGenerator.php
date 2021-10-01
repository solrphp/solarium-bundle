<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Generator;

use JMS\Serializer\SerializerInterface;
use Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;

/**
 * Params Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamsGenerator
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
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array<int, array<string, mixed>> $parameters
     *
     * @return \Generator<int, \Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface>
     */
    public function generate(array $parameters): \Generator
    {
        foreach ($parameters as $parameter) {
            foreach ($parameter['parameter_set_maps'] as $name => $parameterSetMap) {
                $parameter['parameter_set_maps'][$name] = $this->serializer->deserialize(json_encode($parameterSetMap, \JSON_THROW_ON_ERROR), ParameterSetMap::class, $this->format);
            }

            yield new RequestParameters($parameter['cores'], $parameter['parameter_set_maps']);
        }
    }
}
