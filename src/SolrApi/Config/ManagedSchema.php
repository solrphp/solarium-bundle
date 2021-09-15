<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\Contract\SolrApi\CoreDependentConfigInterface;

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
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>
     */
    private ArrayCollection $fields;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\CopyField>
     */
    private ArrayCollection $copyFields;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>
     */
    private ArrayCollection $dynamicFields;

    /**
     * @param string                                                                      $uniqueKey
     * @param ArrayCollection<int, string>|null                                           $cores
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>|null $fields
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\CopyField>|null $copyFields
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>|null $dynamicFields
     */
    public function __construct(string $uniqueKey, ArrayCollection $cores = null, ArrayCollection $fields = null, ArrayCollection $copyFields = null, ArrayCollection $dynamicFields = null)
    {
        $this->uniqueKey = $uniqueKey;
        $this->cores = $cores ?? new ArrayCollection();
        $this->fields = $fields ?? new ArrayCollection();
        $this->copyFields = $copyFields ?? new ArrayCollection();
        $this->dynamicFields = $dynamicFields ?? new ArrayCollection();
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
     * @param ArrayCollection<int, string> $cores
     */
    public function setCores(ArrayCollection $cores): void
    {
        $this->cores = $cores;
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>
     */
    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    /**
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType> $fields
     */
    public function setFields(ArrayCollection $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\CopyField>
     */
    public function getCopyFields(): ArrayCollection
    {
        return $this->copyFields;
    }

    /**
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\CopyField> $copyFields
     */
    public function setCopyFields(ArrayCollection $copyFields): void
    {
        $this->copyFields = $copyFields;
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>
     */
    public function getDynamicFields(): ArrayCollection
    {
        return $this->dynamicFields;
    }

    /**
     * @param ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType> $dynamicFields
     */
    public function setDynamicFields(ArrayCollection $dynamicFields): void
    {
        $this->dynamicFields = $dynamicFields;
    }
}
