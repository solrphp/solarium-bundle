<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Config;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;

/**
 * Managed Schema.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ManagedSchema implements CoreDependentConfigInterface
{
    /**
     * @var string
     */
    private string $uniqueKey;

    /**
     * @var ArrayCollection<int, string>
     */
    private ArrayCollection $cores;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     */
    private ArrayCollection $fields;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField>
     */
    private ArrayCollection $copyFields;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     */
    private ArrayCollection $dynamicFields;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>
     */
    private ArrayCollection $fieldTypes;

    /**
     * @param string                                                                                  $uniqueKey
     * @param ArrayCollection<array-key, string>|null                                                 $cores
     * @param ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>|null     $fields
     * @param ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField>|null $copyFields
     * @param ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>|null     $dynamicFields
     * @param ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>|null $fieldTypes
     */
    public function __construct(string $uniqueKey, ArrayCollection $cores = null, ArrayCollection $fields = null, ArrayCollection $copyFields = null, ArrayCollection $dynamicFields = null, ArrayCollection $fieldTypes = null)
    {
        $this->uniqueKey = $uniqueKey;
        $this->cores = $cores ?? new ArrayCollection();
        $this->fields = $fields ?? new ArrayCollection();
        $this->copyFields = $copyFields ?? new ArrayCollection();
        $this->dynamicFields = $dynamicFields ?? new ArrayCollection();
        $this->fieldTypes = $fieldTypes ?? new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }

    /**
     * @param string $uniqueKey
     */
    public function setUniqueKey(string $uniqueKey): void
    {
        $this->uniqueKey = $uniqueKey;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getCores(): ArrayCollection
    {
        return $this->cores;
    }

    /**
     * @param string $core
     */
    public function addCore(string $core): void
    {
        $this->cores->add($core);
    }

    /**
     * @param string $core
     *
     * @return bool
     */
    public function removeCore(string $core): bool
    {
        return $this->cores->removeElement($core);
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     */
    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $field
     */
    public function addField(Field $field): void
    {
        $this->fields->add($field);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $field
     *
     * @return bool
     */
    public function removeField(Field $field): bool
    {
        return $this->fields->removeElement($field);
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField>
     */
    public function getCopyFields(): ArrayCollection
    {
        return $this->copyFields;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField $field
     */
    public function addCopyField(CopyField $field): void
    {
        $this->copyFields->add($field);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField $field
     *
     * @return bool
     */
    public function removeCopyField(CopyField $field): bool
    {
        return $this->copyFields->removeElement($field);
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     */
    public function getDynamicFields(): ArrayCollection
    {
        return $this->dynamicFields;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $field
     */
    public function addDynamicField(Field $field): void
    {
        $this->dynamicFields->add($field);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $field
     *
     * @return bool
     */
    public function removeDynamicField(Field $field): bool
    {
        return $this->dynamicFields->removeElement($field);
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>
     */
    public function getFieldTypes(): ArrayCollection
    {
        return $this->fieldTypes;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType $fieldType
     */
    public function addFieldType(FieldType $fieldType): void
    {
        $this->fieldTypes->add($fieldType);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType $fieldType
     *
     * @return bool
     */
    public function removeFieldType(FieldType $fieldType): bool
    {
        return $this->fieldTypes->removeElement($fieldType);
    }
}
