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
use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;

/**
 * FieldsResponse.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldsResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @var ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     *
     * @Serializer\Type("ArrayCollection<Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>")
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
     * @return ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
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
}
