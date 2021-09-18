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

use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Response\Header;
use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;

/**
 * Schema Response.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class SchemaResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromSolariumResponse(Response $response): ResponseInterface
    {
        $result = new self();
        $result->body = $response->getBody();

        $header = new Header();
        $header->setStatusCode($response->getHeaders()['status'] ?? -1);
        $header->setQTime($response->getHeaders()['QTime'] ?? -1);

        $result->setHeader($header);

        return $result;
    }
}
