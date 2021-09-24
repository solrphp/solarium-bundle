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
use Solrphp\SolariumBundle\Common\Response\Error;
use Solrphp\SolariumBundle\Common\Response\Header;

/**
 * Response Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ResponseInterface
{
    /**
     * get response header.
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface
     */
    public function getResponseHeader(): ResponseHeaderInterface;

    /**
     * get response error.
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface|null
     */
    public function getError(): ?ResponseErrorInterface;

    /**
     * get response body.
     *
     *  @return string|null
     */
    public function getBody(): ?string;
}
