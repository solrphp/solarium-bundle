<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter;

/**
 * Config Property Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PropertyListHandler implements SubscribingHandlerInterface
{
    /**
     * @return array<int, array<string, int|string>>
     */
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'PropertyList',
                'method' => 'deserializePropertyList',
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'ParameterList',
                'method' => 'deserializeParameterList',
            ],
        ];
    }

    /**
     * @param \JMS\Serializer\Visitor\DeserializationVisitorInterface $visitor
     * @param array<mixed>                                            $data
     * @param array<string, string|array<mixed>>                      $type
     * @param \JMS\Serializer\Context                                 $context
     *
     * @return array<int, Property>
     */
    public function deserializePropertyList(DeserializationVisitorInterface $visitor, array $data, array $type, Context $context): array
    {
        return $this->deserialize($data, Property::class);
    }

    /**
     * @param \JMS\Serializer\Visitor\DeserializationVisitorInterface $visitor
     * @param array<mixed>                                            $data
     * @param array<string, string|array<mixed>>                      $type
     * @param \JMS\Serializer\Context                                 $context
     *
     * @return array<int, Parameter>
     */
    public function deserializeParameterList(DeserializationVisitorInterface $visitor, array $data, array $type, Context $context): array
    {
        return $this->deserialize($data, Parameter::class);
    }

    /**
     * @param array<mixed> $data
     * @param class-string $class
     *
     * @return array<int, mixed>
     */
    private function deserialize(array $data, string $class): array
    {
        $return = [];

        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                if (!isset($value['name'], $value['value'])) {
                    continue;
                }

                $return[] = new $class($value['name'], $value['value']);

                continue;
            }

            if (\is_string($value)) {
                $return[] = new $class($key, $value);
            }
        }

        return $return;
    }
}
