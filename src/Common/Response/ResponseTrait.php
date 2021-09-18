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

use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;

/**
 * ResponseTrait.
 *
 * @author wicliff <wwolda@gmail.com>
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

    /**
     * @param \Solarium\Core\Client\Response $response
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public static function fromSolariumResponse(Response $response): ResponseInterface
    {
        $header = new Header();
        $header->setStatusCode($response->getStatusCode());
        $header->setQTime($response->getHeaders()['QTime'] ?? -1);

        $result = self::getInstance();
        $result->setHeader($header);

        if ($response->getStatusCode() >= 300) {
            $data = json_decode($response->getBody(), true);

            $error = new Error();
            $error->setMetadata($data['metadata'] ?? []);
            $error->setMessage($data['message'] ?? '');
            $error->setCode($data['code'] ?? -1);

            $result->setError($error);

            return $result;
        }

        $result->setBody($response->getBody());

        return $result;
    }

    /**
     * get static.
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    abstract protected static function getInstance(): ResponseInterface;
}
