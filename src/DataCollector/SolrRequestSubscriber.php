<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\DataCollector;

use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecuteRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * SolrSubscriber.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrRequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Solrphp\SolariumBundle\DataCollector\SolrCallRegistry
     */
    private SolrCallRegistry $registry;

    /**
     * @param \Solrphp\SolariumBundle\DataCollector\SolrCallRegistry $registry
     */
    public function __construct(SolrCallRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PreExecuteRequest::class => 'onClientRequest',
            PostExecuteRequest::class => 'onClientResponse',
        ];
    }

    /**
     * @param \Solarium\Core\Event\PreExecuteRequest $event
     */
    public function onClientRequest(PreExecuteRequest $event): void
    {
        $this->registry->addRequest($event);
    }

    /**
     * @param \Solarium\Core\Event\PostExecuteRequest $event
     */
    public function onClientResponse(PostExecuteRequest $event): void
    {
        $this->registry->addResponse($event);
    }
}
