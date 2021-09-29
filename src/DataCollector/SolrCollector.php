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

use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Solr Collector.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCollector extends AbstractDataCollector
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
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $time = 0;
        $calls = [];

        foreach ($this->registry->getCalls() as $call) {
            if (false === isset($call['duration'])) {
                continue;
            }

            $time += $call['duration'];
            $calls[] = $call;
        }

        $this->data = [
            'total' => \count($calls),
            'time' => $time,
            'requests' => $calls,
        ];
    }

    /**
     * Reset.
     */
    public function reset(): void
    {
        $this->data = [
            'total' => 0,
            'time' => 0,
            'requests' => [],
        ];
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->data['total'];
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->data['time'];
    }

    /**
     * @return array<int,array<string, mixed>>
     */
    public function getRequests(): array
    {
        return $this->data['requests'];
    }
}
