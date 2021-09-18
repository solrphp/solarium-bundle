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

use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface;

/**
 * ResponseTrait.
 *
 * @author wicliff <wwolda@gmail.com>
 */
trait ResponseTrait
{
    /**
     * @var \Solrphp\SolariumBundle\Common\Response\Header
     */
    private Header $responseHeader;

    /**
     * @var \Solrphp\SolariumBundle\Common\Response\Error|null
     */
    private ?Error $error = null;

    /**
     * @var string
     */
    private string $body;

    /**
     * {@inheritdoc}
     */
    public function getHeader(): ResponseHeaderInterface
    {
        return $this->responseHeader;
    }

    /**
     * @param \Solrphp\SolariumBundle\Common\Response\Header $responseHeader
     */
    public function setHeader(Header $responseHeader): void
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
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
