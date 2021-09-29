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
use Solarium\Component\RequestBuilder\SubRequest;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecuteRequest;
use Solrphp\SolariumBundle\DataCollector\SolrCallRegistry;
use Symfony\Component\HttpFoundation\InputBag;

/**
 * SolrCallRegistryTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCallRegistryTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAddRequest(): void
    {
        $registry = new SolrCallRegistry();

        self::assertEmpty($registry->getCalls());

        $registry->addRequest($this->getPreExecuteRequestEvent());
        self::assertCount(1, $registry->getCalls());

        $call = current($registry->getCalls());

        self::assertSame('http://127.0.0.1:8983/solr/foo/bar', $call['resource']);
        self::assertInstanceOf(InputBag::class, $call['request_headers']);
        self::assertSame(['Content-Type: application/json;'], $call['request_headers']->all());
        self::assertInstanceOf(InputBag::class, $call['request_options']);
        self::assertSame(['method' => 'GET', 'api' => 'v1', 'handler' => 'bar', 'bar' => 'baz'], $call['request_options']->all());
        self::assertInstanceOf(InputBag::class, $call['request_params']);
        self::assertSame(['wt' => 'json'], $call['request_params']->all());
        self::assertSame('{"foo": "bar}', $call['request_body']);
        self::assertSame('', $call['response_headers']);
        self::assertSame('', $call['response_body']);
        self::assertArrayHasKey('start', $call);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAddResponse(): void
    {
        $registry = new SolrCallRegistry();

        self::assertEmpty($registry->getCalls());

        $registry->addResponse($this->getPostExecuteRequestEvent());
        self::assertCount(1, $registry->getCalls());

        $call = current($registry->getCalls());

        self::assertSame(['HTTP 200 OK'], $call['response_headers']->all());

        $expected = <<<'JSON'
{
    "baz": "qux"
}
JSON;

        self::assertSame($expected, $call['response_body']);
        self::assertArrayHasKey('end', $call);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRequestResponseSameId(): void
    {
        $request = $this->getRequest('schema.xml');
        $endpoint = $this->getEndpoint();
        $response = $this->getResponse();

        $registry = new SolrCallRegistry();
        $registry->addRequest(new PreExecuteRequest($request, $endpoint));
        $registry->addResponse(new PostExecuteRequest($request, $endpoint, $response));

        self::assertCount(1, $registry->getCalls());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\HttpException
     */
    public function testDuration(): void
    {
        $request = $this->getRequest('schema.xml');
        $endpoint = $this->getEndpoint();
        $response = $this->getResponse();

        $registry = new SolrCallRegistry();
        $registry->addRequest(new PreExecuteRequest($request, $endpoint));
        $registry->addResponse(new PostExecuteRequest($request, $endpoint, $response));
        $call = current($registry->getCalls());

        self::assertTrue($call['duration'] > 0);
        self::assertTrue($call['duration'] < 0.5);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRequestUnknownFormat(): void
    {
        $request = $this->getRequest('yaml');
        $endpoint = $this->getEndpoint();
        $response = $this->getResponse();

        $registry = new SolrCallRegistry();
        $registry->addRequest(new PreExecuteRequest($request, $endpoint));
        $registry->addResponse(new PostExecuteRequest($request, $endpoint, $response));

        self::assertCount(1, $registry->getCalls());

        $call = current($registry->getCalls());
        self::assertSame('{"foo": "bar}', $call['request_body']);
        self::assertSame('{"baz": "qux"}', $call['response_body']);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\HttpException
     */
    public function testInvalidFormat(): void
    {
        $request = $this->getRequest(new SubRequest());
        $endpoint = $this->getEndpoint();
        $response = $this->getResponse();

        $registry = new SolrCallRegistry();
        $registry->addRequest(new PreExecuteRequest($request, $endpoint));
        $registry->addResponse(new PostExecuteRequest($request, $endpoint, $response));

        self::assertCount(1, $registry->getCalls());

        $call = current($registry->getCalls());

        $expectedResponse = <<<'JSON'
{
    "baz": "qux"
}
JSON;

        self::assertSame('{"foo": "bar}', $call['request_body']);
        self::assertSame($expectedResponse, $call['response_body']);
    }

    /**
     * @return \Solarium\Core\Event\PostExecuteRequest
     */
    public function getPostExecuteRequestEvent(): PostExecuteRequest
    {
        return new PostExecuteRequest($this->getRequest(), $this->getEndpoint(), $this->getResponse());
    }

    /**
     * @return \Solarium\Core\Event\PreExecuteRequest
     */
    private function getPreExecuteRequestEvent(): PreExecuteRequest
    {
        return new PreExecuteRequest($this->getRequest(), $this->getEndpoint());
    }

    /**
     * @return \Solarium\Core\Client\Endpoint
     */
    private function getEndpoint(): Endpoint
    {
        return new Endpoint(['core' => 'foo']);
    }

    /**
     * @param string|subRequest|null $wt
     *
     * @return \Solarium\Core\Client\Request
     */
    private function getRequest($wt = 'json'): Request
    {
        $request = new Request();
        $request->addParam('wt', $wt);
        $request->setHandler('bar');
        $request->setHeaders(['Content-Type: application/json;']);
        $request->setOptions(['bar' => 'baz']);
        $request->setRawData('{"foo": "bar}');

        return $request;
    }

    /**
     * @return \Solarium\Core\Client\Response
     *
     * @throws \Solarium\Exception\HttpException
     */
    private function getResponse(): Response
    {
        $response = new Response('{"baz": "qux"}');
        $response->setHeaders(['HTTP 200 OK']);

        return $response;
    }
}
