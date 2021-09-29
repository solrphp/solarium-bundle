<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\DataCollector;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecuteRequest;
use Solrphp\SolariumBundle\DataCollector\SolrCallRegistry;
use Solrphp\SolariumBundle\DataCollector\SolrRequestSubscriber;

/**
 * SolrRequestSubscriberTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrRequestSubscriberTest extends TestCase
{
    /**
     * test events.
     */
    public function testEvents(): void
    {
        $subscriber = new SolrRequestSubscriber($this->getRegistry());

        $subscriber->onClientRequest($this->getPreExecuteRequest());
        $subscriber->onClientResponse($this->getPostExecuteRequest());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSubscribedEvents(): void
    {
        self::assertArrayHasKey(PreExecuteRequest::class, SolrRequestSubscriber::getSubscribedEvents());
        self::assertArrayHasKey(PostExecuteRequest::class, SolrRequestSubscriber::getSubscribedEvents());
    }

    /**
     * @return \Solrphp\SolariumBundle\DataCollector\SolrCallRegistry
     */
    public function getRegistry(): SolrCallRegistry
    {
        $registry = $this->getMockBuilder(SolrCallRegistry::class)->getMock();
        $registry->expects(self::once())->method('addRequest')->with($this->getPreExecuteRequest());
        $registry->expects(self::once())->method('addResponse')->with($this->getPostExecuteRequest());

        return $registry;
    }

    /**
     * @return \Solarium\Core\Event\PreExecuteRequest
     */
    private function getPreExecuteRequest(): PreExecuteRequest
    {
        $request = new Request();
        $endpoint = new Endpoint();

        return new PreExecuteRequest($request, $endpoint);
    }

    /**
     * @return \Solarium\Core\Event\PostExecuteRequest
     */
    private function getPostExecuteRequest(): PostExecuteRequest
    {
        $request = new Request();
        $endpoint = new Endpoint();
        $response = new Response('{}');

        return new PostExecuteRequest($request, $endpoint, $response);
    }
}
