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

use JMS\Serializer\Annotation as Serializer;

/**
 * UpdateLog.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UpdateLog implements \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $dir;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $numRecordsToKeep = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $maxNumLogsToKeep = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $numVersionBuckets = null;

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     */
    public function setDir(string $dir): void
    {
        $this->dir = $dir;
    }

    /**
     * @return int|null
     */
    public function getNumRecordsToKeep(): ?int
    {
        return $this->numRecordsToKeep;
    }

    /**
     * @param int|null $numRecordsToKeep
     */
    public function setNumRecordsToKeep(?int $numRecordsToKeep): void
    {
        $this->numRecordsToKeep = $numRecordsToKeep;
    }

    /**
     * @return int|null
     */
    public function getMaxNumLogsToKeep(): ?int
    {
        return $this->maxNumLogsToKeep;
    }

    /**
     * @param int|null $maxNumLogsToKeep
     */
    public function setMaxNumLogsToKeep(?int $maxNumLogsToKeep): void
    {
        $this->maxNumLogsToKeep = $maxNumLogsToKeep;
    }

    /**
     * @return int|null
     */
    public function getNumVersionBuckets(): ?int
    {
        return $this->numVersionBuckets;
    }

    /**
     * @param int|null $numVersionBuckets
     */
    public function setNumVersionBuckets(?int $numVersionBuckets): void
    {
        $this->numVersionBuckets = $numVersionBuckets;
    }

    /**
     * @return array<string, string|int>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'dir' => $this->dir,
                'numRecordsToKeep' => $this->numRecordsToKeep,
                'maxNumLogsToKeep' => $this->maxNumLogsToKeep,
                'numVersionBuckets' => $this->numVersionBuckets,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
