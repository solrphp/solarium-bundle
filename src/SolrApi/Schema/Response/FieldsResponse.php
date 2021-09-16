<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Response;

use Doctrine\Common\Collections\ArrayCollection;
use Solrphp\SolariumBundle\Response\AbstractResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;

/**
 * FieldsResponse.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldsResponse extends AbstractResponse
{
    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>
     */
    private ArrayCollection $fields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>
     */
    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType $field
     */
    public function addField(FieldType $field): void
    {
        $this->fields->add($field);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType $field
     */
    public function removeField(FieldType $field): void
    {
        $this->fields->removeElement($field);
    }
}
