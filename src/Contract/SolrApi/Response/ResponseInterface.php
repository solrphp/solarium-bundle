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
     * get response error.
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface|null
     */
    public function getError(): ?ResponseErrorInterface;

    /**
     * @return string
     */
    public function getBody(): string;

    /**
     * @param \Solarium\Core\Client\Response $response
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public static function fromSolariumResponse(Response $response): self;
}
