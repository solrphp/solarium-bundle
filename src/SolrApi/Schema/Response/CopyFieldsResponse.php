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
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;

/**
 * Copy Fields Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CopyFieldsResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @var ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField>
     *
     * @Serializer\Type("ArrayCollection<Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField>")
     */
    private ArrayCollection $copyFields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->copyFields = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<int, \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField>
     */
    public function getCopyFields(): ArrayCollection
    {
        return $this->copyFields;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField $copyField
     */
    public function addCopyField(CopyField $copyField): void
    {
        $this->copyFields->add($copyField);
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField $copyField
     *
     * @return bool
     */
    public function removeCopyField(CopyField $copyField): bool
    {
        return $this->copyFields->removeElement($copyField);
    }
}
