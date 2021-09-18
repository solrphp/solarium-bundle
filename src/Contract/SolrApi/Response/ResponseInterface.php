<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\SolrApi\Response;

use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Response\Header;

/**
 * Response Interface.
 *
 * @author wicliff <wwolda@gmail.com>
 */
interface ResponseInterface
{
    /**
     * get response header.
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface
     */
    public function getHeader(): ResponseHeaderInterface;

    /**
     * set response header.
     *
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface $header
     */
    public function setHeader(ResponseHeaderInterface $header): void;

    /**
     * get response error.
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface|null
     */
    public function getError(): ?ResponseErrorInterface;

    /**
     * set response error.
     *
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface|null $error
     */
    public function setError(?ResponseErrorInterface $error): void;

    /**
     * @return string|null
     */
    public function getBody(): ?string;

    /**
     * @param string|null $body
     */
    public function setBody(?string $body): void;

    /**
     * todo: have the serializer take care of this?
     *
     * @param \Solarium\Core\Client\Response $response
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public static function fromSolariumResponse(Response $response): self;
}
