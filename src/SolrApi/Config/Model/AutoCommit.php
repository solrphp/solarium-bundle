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
 * AutoCommit.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AutoCommit implements \JsonSerializable
{
    /**
     * @var int|null
     */
    private ?int $maxDocs = null;

    /**
     * @var int|null
     */
    private ?int $maxTime = null;

    /**
     * @var string|null
     */
    private ?string $maxSize = null;

    /**
     * @var bool|null
     */
    private ?bool $openSearcher = null;

    /**
     * @return int|null
     */
    public function getMaxDocs(): ?int
    {
        return $this->maxDocs;
    }

    /**
     * @param int|null $maxDocs
     */
    public function setMaxDocs(?int $maxDocs): void
    {
        $this->maxDocs = $maxDocs;
    }

    /**
     * @return int|null
     */
    public function getMaxTime(): ?int
    {
        return $this->maxTime;
    }

    /**
     * @param int|null $maxTime
     */
    public function setMaxTime(?int $maxTime): void
    {
        $this->maxTime = $maxTime;
    }

    /**
     * @return string|null
     */
    public function getMaxSize(): ?string
    {
        return $this->maxSize;
    }

    /**
     * @param string|null $maxSize
     */
    public function setMaxSize(?string $maxSize): void
    {
        $this->maxSize = $maxSize;
    }

    /**
     * @return bool|null
     */
    public function getOpenSearcher(): ?bool
    {
        return $this->openSearcher;
    }

    /**
     * @param bool|null $openSearcher
     */
    public function setOpenSearcher(?bool $openSearcher): void
    {
        $this->openSearcher = $openSearcher;
    }

    /**
     * @return array<string, int|string|bool>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'maxDocs' => $this->maxDocs,
                'maxTime' => $this->maxTime,
                'maxSize' => $this->maxSize,
                'openSearcher' => $this->openSearcher,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
