<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Response;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\CopyFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\DynamicFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldTypeResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\SchemaResponse;

/**
 * CopyFieldsResponseTest.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class SchemaApiResponseTest extends TestCase
{
    /**
     * @dataProvider classProvider
     *
     * @param class-string $class
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponse(string $class): void
    {
        self::assertInstanceOf(ResponseInterface::class, new $class());

        $response = $class::fromSolariumResponse(new Response('foo', ['HTTP 200 OK', 'QTime' => 4]));
        $header = $response->getHeader();

        self::assertInstanceOf(ResponseHeaderInterface::class, $header);
        self::assertSame(200, $header->getStatusCode());
        self::assertSame(4, $header->getQTime());
        self::assertSame('foo', $response->getBody());
    }

    /**
     * @dataProvider classProvider
     *
     * @param class-string $class
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseNoQtime(string $class): void
    {
        $response = $class::fromSolariumResponse(new Response('foo', ['HTTP 200 OK']));

        self::assertSame(-1, $response->getHeader()->getQTime());
    }

    /**
     * @dataProvider classProvider
     *
     * @param class-string $class
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseWithErrorAndBody(string $class): void
    {
        $response = $class::fromSolariumResponse(new Response(json_encode(['metadata' => ['foo', 'bar'], 'message' => 'foo', 'code' => 2]), ['HTTP 400 OK']));

        $error = $response->getError();

        self::assertInstanceOf(ResponseErrorInterface::class, $error);
        self::assertSame(['foo', 'bar'], $error->getMetaData());
        self::assertSame('foo', $error->getMessage());
        self::assertSame(2, $error->getCode());
    }

    /**
     * @dataProvider classProvider
     *
     * @param class-string $class
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseWithErrorNoBody(string $class): void
    {
        $response = $class::fromSolariumResponse(new Response('', ['HTTP 400 OK']));

        $error = $response->getError();

        self::assertInstanceOf(ResponseErrorInterface::class, $error);
        self::assertSame([], $error->getMetaData());
        self::assertSame('', $error->getMessage());
        self::assertSame(-1, $error->getCode());
    }

    /**
     * @dataProvider classProvider
     *
     * @param class-string $class
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromSolariumResponseWithDifferentResponseCodes(string $class): void
    {
        $response = $class::fromSolariumResponse(new Response('', ['HTTP 299 OK']));

        self::assertNull($response->getError());

        $response = $class::fromSolariumResponse(new Response('', ['HTTP 300 OK']));

        self::assertNotNull($response->getError());
    }

    /**
     * @dataProvider classProvider
     *
     * @param class-string $class
     */
    public function testInstanceInjection(string $class): void
    {
        $this->expectError();
        $this->expectErrorMessageMatches('/^Call to protected method .*/');
        $instance = new $class();
        $instance::getInstance();
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testCopyFieldResponse(): void
    {
        $response = new CopyFieldsResponse();
        $field = new CopyField();

        $response->addCopyField($field);

        self::assertContains($field, $response->getCopyFields());
        self::assertTrue($response->removeCopyField($field));
        self::assertFalse($response->removeCopyField($field));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDynamicFieldResponse(): void
    {
        $response = new DynamicFieldsResponse();
        $field = new Field();

        $response->addDynamicField($field);

        self::assertContains($field, $response->getDynamicFields());
        self::assertTrue($response->removeDynamicField($field));
        self::assertFalse($response->removeDynamicField($field));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFieldsResponse(): void
    {
        $response = new FieldsResponse();
        $field = new Field();

        $response->addField($field);

        self::assertContains($field, $response->getFields());
        self::assertTrue($response->removeField($field));
        self::assertFalse($response->removeField($field));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFieldTypesResponse(): void
    {
        $response = new FieldTypeResponse();
        $field = new FieldType();

        $response->addFieldType($field);

        self::assertContains($field, $response->getFieldTypes());
        self::assertTrue($response->removeFieldType($field));
        self::assertFalse($response->removeFieldType($field));
    }

    /**
     * @return \Generator<string, array>
     */
    public function classProvider(): \Generator
    {
        yield 'copy_field_response' => [
            'class' => CopyFieldsResponse::class,
        ];

        yield 'dynamic_field_response' => [
            'class' => DynamicFieldsResponse::class,
        ];

        yield 'field_response' => [
            'class' => FieldsResponse::class,
        ];

        yield 'field_type_response' => [
            'class' => FieldTypeResponse::class,
        ];

        yield 'schema_response' => [
            'class' => SchemaResponse::class,
        ];
    }
}
