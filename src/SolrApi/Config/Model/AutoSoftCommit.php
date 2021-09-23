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
 * AutoSoftCommit.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AutoSoftCommit implements \JsonSerializable
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
     * @return array<string, int>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'maxDocs' => $this->maxDocs,
                'maxTime' => $this->maxTime,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
