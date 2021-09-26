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
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;

/**
 * Dynamic Fields Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DynamicFieldsResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @var ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     *
     * @Serializer\Type("ArrayCollection<Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>")
     */
    private ArrayCollection $dynamicFields;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->dynamicFields = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<array-key, \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field>
     */
    public function getDynamicFields(): ArrayCollection
    {
        return $this->dynamicFields;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $dynamicField
     */
    public function addDynamicField(Field $dynamicField): void
    {
        $this->dynamicFields->add($dynamicField);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Field $dynamicField
     *
     * @return bool
     */
    public function removeDynamicField(Field $dynamicField): bool
    {
        return $this->dynamicFields->removeElement($dynamicField);
    }
}
