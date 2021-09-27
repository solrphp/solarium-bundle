<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * ResponseTrait.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
trait ResponseTrait
{
    /**
     * @var \Solrphp\SolariumBundle\Common\Response\Header
     *
     * @Serializer\SerializedName("responseHeader")
     * @Serializer\Type("Solrphp\SolariumBundle\Common\Response\Header")
     */
    private Header $responseHeader;

    /**
     * @var \Solrphp\SolariumBundle\Common\Response\Error|null
     *
     * @Serializer\Type("Solrphp\SolariumBundle\Common\Response\Error")
     */
    private ?Error $error = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $body = null;

    /**
     * {@inheritdoc}
     */
    public function getResponseHeader(): Header
    {
        return $this->responseHeader;
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Response\Header $responseHeader
     */
    public function setResponseHeader(Header $responseHeader): void
    {
        $this->responseHeader = $responseHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getError(): ?Error
    {
        return $this->error;
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Response\Error|null $error
     */
    public function setError(?Error $error): void
    {
        $this->error = $error;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }
}
