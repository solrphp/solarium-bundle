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

use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface;

/**
 * ResponseTrait.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
trait ResponseTrait
{
    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface
     */
    private ResponseHeaderInterface $responseHeader;

    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface|null
     */
    private ?ResponseErrorInterface $error = null;

    /**
     * @var string|null
     */
    private ?string $body = null;

    /**
     * {@inheritdoc}
     */
    public function getHeader(): ResponseHeaderInterface
    {
        return $this->responseHeader;
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface $responseHeader
     */
    public function setHeader(ResponseHeaderInterface $responseHeader): void
    {
        $this->responseHeader = $responseHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getError(): ?ResponseErrorInterface
    {
        return $this->error;
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface|null $error
     */
    public function setError(?ResponseErrorInterface $error): void
    {
        $this->error = $error;
    }

    /**
     * @return string|null
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
