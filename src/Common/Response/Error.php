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
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseErrorInterface;

/**
 * Error.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Error implements ResponseErrorInterface
{
    /**
     * @var string[]
     *
     * @Serializer\Type("array<string>")
     */
    private array $metadata = [];

    /**
     * @var array<int, array<string, array<string>>>
     *
     * @Serializer\Type("array")
     */
    private array $details = [];

    /**
     * @var string|null
     */
    private ?string $trace = null;

    /**
     * @var string|null
     *
     * @Serializer\SerializedName("msg")
     */
    private ?string $message = null;

    /**
     * @var int
     */
    private int $code;

    /**
     * @return string[]
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param string[] $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @param array<int, array<string, array<string>>> $details
     */
    public function setDetails(array $details): void
    {
        $this->details = $details;
    }

    /**
     * @return string|null
     */
    public function getTrace(): ?string
    {
        return $this->trace;
    }

    /**
     * @param string|null $trace
     */
    public function setTrace(?string $trace): void
    {
        $this->trace = $trace;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }
}
