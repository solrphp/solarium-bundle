<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\SolrApi\Config\FieldType;

/**
 * Dynamic Fields Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DynamicFieldsResponse extends AbstractResponse
{
    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>
     */
    private ArrayCollection $dynamicFields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->dynamicFields = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Config\FieldType>
     */
    public function getDynamicFields(): ArrayCollection
    {
        return $this->dynamicFields;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\FieldType $dynamicField
     */
    public function addDynamicField(FieldType $dynamicField): void
    {
        $this->dynamicFields->add($dynamicField);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\FieldType $dynamicField
     */
    public function removeDynamicField(FieldType $dynamicField): void
    {
        $this->getDynamicFields()->removeElement($dynamicField);
    }
}
