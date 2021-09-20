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
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\CopyFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\DynamicFieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldsResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldTypeResponse;

/**
 * CopyFieldsResponseTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaApiResponseTest extends TestCase
{
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
}
