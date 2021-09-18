<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Config;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;

/**
 * ManagedSchema Test.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class ManagedSchemaTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testManagedSchemaConstructor(): void
    {
        $cores = $this->getCores();
        $fields = $this->getFields();
        $copyFields = $this->getCopyFields();
        $dynamicFields = $this->getFields();
        $fieldTypes = $this->getFieldTypes();

        $managedSchema = new ManagedSchema('foo', $cores, $fields, $copyFields, $dynamicFields, $fieldTypes);

        self::assertSame('foo', $managedSchema->getUniqueKey());
        self::assertSame($cores, $managedSchema->getCores());
        self::assertSame($fields, $managedSchema->getFields());
        self::assertSame($copyFields, $managedSchema->getCopyFields());
        self::assertSame($dynamicFields, $managedSchema->getDynamicFields());
        self::assertSame($fieldTypes, $managedSchema->getFieldTypes());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testManagedSchemaAccessors(): void
    {
        $key = 'foo';
        $core = $this->getCores()->first();
        $field = $this->getFields()->first();
        $copyField = $this->getCopyFields()->first();
        $dynamicField = $this->getFields()->first();
        $fieldType = $this->getFieldTypes()->first();

        $managedSchema = new ManagedSchema('foo');

        $managedSchema->setUniqueKey($key);
        $managedSchema->addCore($core);
        $managedSchema->addField($field);
        $managedSchema->addCopyField($copyField);
        $managedSchema->addDynamicField($dynamicField);
        $managedSchema->addFieldType($fieldType);

        self::assertSame($key, $managedSchema->getUniqueKey());
        self::assertContains($core, $managedSchema->getCores());
        self::assertContains($field, $managedSchema->getFields());
        self::assertContains($copyField, $managedSchema->getCopyFields());
        self::assertContains($dynamicField, $managedSchema->getDynamicFields());
        self::assertContains($fieldType, $managedSchema->getFieldTypes());

        self::assertTrue($managedSchema->removeCore($core));
        self::assertFalse($managedSchema->removeCore($core));

        self::assertTrue($managedSchema->removeField($field));
        self::assertFalse($managedSchema->removeField($field));

        self::assertTrue($managedSchema->removeCopyField($copyField));
        self::assertFalse($managedSchema->removeCopyField($copyField));

        self::assertTrue($managedSchema->removeDynamicField($dynamicField));
        self::assertFalse($managedSchema->removeDynamicField($dynamicField));

        self::assertTrue($managedSchema->removeFieldType($fieldType));
        self::assertFalse($managedSchema->removeFieldType($fieldType));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOptionalArguments(): void
    {
        $managedSchema = new ManagedSchema('foo');

        self::assertInstanceOf(ArrayCollection::class, $managedSchema->getCores());
        self::assertEmpty($managedSchema->getCores());

        self::assertInstanceOf(ArrayCollection::class, $managedSchema->getFieldTypes());
        self::assertEmpty($managedSchema->getFieldTypes());

        self::assertInstanceOf(ArrayCollection::class, $managedSchema->getFields());
        self::assertEmpty($managedSchema->getFields());

        self::assertInstanceOf(ArrayCollection::class, $managedSchema->getCopyFields());
        self::assertEmpty($managedSchema->getCopyFields());

        self::assertInstanceOf(ArrayCollection::class, $managedSchema->getDynamicFields());
        self::assertEmpty($managedSchema->getDynamicFields());
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>
     */
    public function getFieldTypes(): ArrayCollection
    {
        $return = new ArrayCollection();

        for ($i = 0; $i < 3; ++$i) {
            $field = new FieldType();
            $field->setClass('foo');
            $field->setName('bar');

            $return->add($field);
        }

        return $return;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    private function getCores(): ArrayCollection
    {
        return new ArrayCollection([
            'foo',
            'bar',
            'baz',
        ]);
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     */
    private function getFields(): ArrayCollection
    {
        $return = new ArrayCollection();

        for ($i = 0; $i < 3; ++$i) {
            $field = new Field();
            $field->setName('foo');

            $return->add($field);
        }

        return $return;
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField>
     */
    private function getCopyFields(): ArrayCollection
    {
        $return = new ArrayCollection();

        for ($i = 0; $i < 3; ++$i) {
            $field = new CopyField();
            $field->setSource('foo');
            $field->setDest('bar');

            $return->add($field);
        }

        return $return;
    }
}
