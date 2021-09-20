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
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;

/**
 * FieldType Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldTypeResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>
     */
    private ArrayCollection $fieldTypes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fieldTypes = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType>
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

    /**
     * {@inheritdoc}
     */
    protected static function getInstance(): ResponseInterface
    {
        return new self();
    }
}
