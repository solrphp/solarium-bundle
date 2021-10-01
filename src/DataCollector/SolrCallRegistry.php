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
use Solrphp\SolariumBundle\DataCollector\Util\CollectorUtil;

/**
 * Solr Call Registry.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCallRegistry
{
    /**
     * @var array<int,array<string, mixed>>
     */
    private array $calls = [];

    /**
     * @return array<int,array<string, mixed>>
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * @param \Solarium\Core\Event\PreExecuteRequest $event
     */
    public function addRequest(PreExecuteRequest $event): void
    {
        $this->calls[spl_object_id($event->getRequest())] = CollectorUtil::fromRequest($event->getRequest(), $event->getEndpoint());
    }

    /**
     * @param \Solarium\Core\Event\PostExecuteRequest $event
     */
    public function addResponse(PostExecuteRequest $event): void
    {
        $request = $event->getRequest();
        $id = spl_object_id($request);
        $call = $this->calls[$id] ?? [];

        $this->calls[$id] = array_merge($call, CollectorUtil::fromResponse($request, $event->getResponse()));
        $this->calls[$id]['duration'] = ($this->calls[$id]['end'] - (isset($call['start']) ? $call['start'] : $this->calls[$id]['end']));
    }
}
