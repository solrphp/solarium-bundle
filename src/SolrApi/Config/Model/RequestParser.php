<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Model;

/**
 * RequestParser.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestParser implements \JsonSerializable
{
    /**
     * @var bool|null
     */
    private ?bool $enableRemoteStreaming = null;

    /**
     * @var bool|null
     */
    private ?bool $enableStreamBody = null;

    /**
     * @var int|null
     */
    private ?int $multipartUploadLimitInKB = null;

    /**
     * @var int|null
     */
    private ?int $formdataUploadLimitInKB = null;

    /**
     * @var bool|null
     */
    private ?bool $addHttpRequestToContext = null;

    /**
     * @return bool|null
     */
    public function getEnableRemoteStreaming(): ?bool
    {
        return $this->enableRemoteStreaming;
    }

    /**
     * @param bool|null $enableRemoteStreaming
     */
    public function setEnableRemoteStreaming(?bool $enableRemoteStreaming): void
    {
        $this->enableRemoteStreaming = $enableRemoteStreaming;
    }

    /**
     * @return bool|null
     */
    public function getEnableStreamBody(): ?bool
    {
        return $this->enableStreamBody;
    }

    /**
     * @param bool|null $enableStreamBody
     */
    public function setEnableStreamBody(?bool $enableStreamBody): void
    {
        $this->enableStreamBody = $enableStreamBody;
    }

    /**
     * @return int|null
     */
    public function getMultipartUploadLimitInKB(): ?int
    {
        return $this->multipartUploadLimitInKB;
    }

    /**
     * @param int|null $multipartUploadLimitInKB
     */
    public function setMultipartUploadLimitInKB(?int $multipartUploadLimitInKB): void
    {
        $this->multipartUploadLimitInKB = $multipartUploadLimitInKB;
    }

    /**
     * @return int|null
     */
    public function getFormdataUploadLimitInKB(): ?int
    {
        return $this->formdataUploadLimitInKB;
    }

    /**
     * @param int|null $formdataUploadLimitInKB
     */
    public function setFormdataUploadLimitInKB(?int $formdataUploadLimitInKB): void
    {
        $this->formdataUploadLimitInKB = $formdataUploadLimitInKB;
    }

    /**
     * @return bool|null
     */
    public function getAddHttpRequestToContext(): ?bool
    {
        return $this->addHttpRequestToContext;
    }

    /**
     * @param bool|null $addHttpRequestToContext
     */
    public function setAddHttpRequestToContext(?bool $addHttpRequestToContext): void
    {
        $this->addHttpRequestToContext = $addHttpRequestToContext;
    }

    /**
     * @return array<string, int|bool>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'enableRemoteStreaming' => $this->enableRemoteStreaming,
                'enableStreamBody' => $this->enableStreamBody,
                'multipartUploadLimitInKB' => $this->multipartUploadLimitInKB,
                'formdataUploadLimitInKB' => $this->formdataUploadLimitInKB,
                'addHttpRequestToContext' => $this->addHttpRequestToContext,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
