<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer\EventDispatcher;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;

/**
 * Solr Pre-Deserialize EventSubscriber.
 *
 * modifies response data and deserialization type.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrPreDeserializeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Closure
     */
    private \Closure $prepare;

    /**
     * @param \Closure $prepare
     */
    public function __construct(\Closure $prepare)
    {
        $this->prepare = $prepare;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json',
                'class' => ResponseInterface::class,
            ],
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'normalizeParamsResponse',
                'format' => 'json',
                'class' => ResponseInterface::class,
            ],
        ];
    }

    /**
     * @param \JMS\Serializer\EventDispatcher\PreDeserializeEvent $event
     */
    public function onPreDeserialize(PreDeserializeEvent $event): void
    {
        $realClass = $event->getContext()->getAttribute('solrphp.real_class');

        $event->setData($this->prepare->__invoke($event->getData()));
        $event->setType($realClass, $event->getType()['params'] ?? []);
    }

    /**
     * @param \JMS\Serializer\EventDispatcher\PreDeserializeEvent $event
     */
    public function normalizeParamsResponse(PreDeserializeEvent $event): void
    {
        if (ParamResponse::class !== $event->getContext()->getAttribute('solrphp.real_class')) {
            return;
        }

        $data = $event->getData();

        if (!isset($data['response']['params'])) {
            return;
        }

        $data['params'] = [];

        foreach ($data['response']['params'] as $name => $map) {
            unset($map['']);
            $data['params'][] = array_merge(
                array_intersect_key($map, array_flip(['_appends_', '_invariants_'])),
                [
                    'parameters' => array_diff_key($map, array_flip(['_appends_', '_invariants_'])),
                    'name' => $name,
                ]
            );
        }

        unset($data['response']);

        $event->setData($data);
    }
}
