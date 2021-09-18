<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\CoreAdmin\Response;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Response\Error;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\CoreResponse;

/**
 * CoreResponse Test.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class CoreResponseTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponse(): void
    {
        $response = CoreResponse::fromSolariumResponse(new Response('foo', ['HTTP 200 OK', 'QTime' => 4]));
        $header = $response->getHeader();

        self::assertInstanceOf(ResponseHeaderInterface::class, $header);
        self::assertSame(200, $header->getStatusCode());
        self::assertSame(4, $header->getQTime());
        self::assertSame('foo', $response->getBody());

        $error = new Error();
        $response->setError($error);

        self::assertSame($error, $response->getError());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseNoQtime(): void
    {
        $response = CoreResponse::fromSolariumResponse(new Response('foo', ['HTTP 200 OK']));

        self::assertSame(-1, $response->getHeader()->getQTime());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseWithErrorAndBody(): void
    {
        $response = CoreResponse::fromSolariumResponse(new Response(json_encode(['metadata' => ['foo', 'bar'], 'message' => 'foo', 'code' => 2]), ['HTTP 400 OK']));

        $error = $response->getError();

        self::assertInstanceOf(ResponseErrorInterface::class, $error);
        self::assertSame(['foo', 'bar'], $error->getMetaData());
        self::assertSame('foo', $error->getMessage());
        self::assertSame(2, $error->getCode());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseWithErrorNoBody(): void
    {
        $response = CoreResponse::fromSolariumResponse(new Response('', ['HTTP 400 OK']));

        $error = $response->getError();

        self::assertInstanceOf(ResponseErrorInterface::class, $error);
        self::assertSame([], $error->getMetaData());
        self::assertSame('', $error->getMessage());
        self::assertSame(-1, $error->getCode());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseWithDifferentResponseCodes(): void
    {
        $response = CoreResponse::fromSolariumResponse(new Response('', ['HTTP 299 OK']));

        self::assertNull($response->getError());

        $response = CoreResponse::fromSolariumResponse(new Response('', ['HTTP 300 OK']));

        self::assertNotNull($response->getError());
    }

    /**
     * test instance injection.
     */
    public function testInstanceInjection(): void
    {
        $this->expectError();
        $this->expectErrorMessageMatches('/^Call to protected method .*/');

        $instance = new CoreResponse();
        $instance::getInstance();
    }
}
