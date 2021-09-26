<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model;

use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\Common\Util\DateTimeUtil;

/**
 * Index.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Index implements \JsonSerializable
{
    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $numDocs;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $maxDoc;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $deletedDocs;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $indexHeapUsageBytes;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $version;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $segmentCount;

    /**
     * @var bool
     *
     * @Serializer\Type("bool")
     */
    private bool $current;

    /**
     * @var bool
     *
     * @Serializer\Type("bool")
     */
    private bool $hasDeletions;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $directory;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $segmentsFile;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\UserData
     *
     * @Serializer\Type("Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\UserData")
     */
    private UserData $userData;

    /**
     * @var \DateTime
     *
     * @Serializer\Type("DateTime")
     * @Serializer\Accessor(setter="setLastModified")
     */
    private \DateTime $lastModified;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $sizeInBytes;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $size;

    /**
     * @return int
     */
    public function getNumDocs(): int
    {
        return $this->numDocs;
    }

    /**
     * @param int $numDocs
     */
    public function setNumDocs(int $numDocs): void
    {
        $this->numDocs = $numDocs;
    }

    /**
     * @return int
     */
    public function getMaxDoc(): int
    {
        return $this->maxDoc;
    }

    /**
     * @param int $maxDoc
     */
    public function setMaxDoc(int $maxDoc): void
    {
        $this->maxDoc = $maxDoc;
    }

    /**
     * @return int
     */
    public function getDeletedDocs(): int
    {
        return $this->deletedDocs;
    }

    /**
     * @param int $deletedDocs
     */
    public function setDeletedDocs(int $deletedDocs): void
    {
        $this->deletedDocs = $deletedDocs;
    }

    /**
     * @return int
     */
    public function getIndexHeapUsageBytes(): int
    {
        return $this->indexHeapUsageBytes;
    }

    /**
     * @param int $indexHeapUsageBytes
     */
    public function setIndexHeapUsageBytes(int $indexHeapUsageBytes): void
    {
        $this->indexHeapUsageBytes = $indexHeapUsageBytes;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getSegmentCount(): int
    {
        return $this->segmentCount;
    }

    /**
     * @param int $segmentCount
     */
    public function setSegmentCount(int $segmentCount): void
    {
        $this->segmentCount = $segmentCount;
    }

    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @param bool $current
     */
    public function setCurrent(bool $current): void
    {
        $this->current = $current;
    }

    /**
     * @return bool
     */
    public function isHasDeletions(): bool
    {
        return $this->hasDeletions;
    }

    /**
     * @param bool $hasDeletions
     */
    public function setHasDeletions(bool $hasDeletions): void
    {
        $this->hasDeletions = $hasDeletions;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     */
    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getSegmentsFile(): string
    {
        return $this->segmentsFile;
    }

    /**
     * @param string $segmentsFile
     */
    public function setSegmentsFile(string $segmentsFile): void
    {
        $this->segmentsFile = $segmentsFile;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\UserData
     */
    public function getUserData(): UserData
    {
        return $this->userData;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\UserData $userData
     */
    public function setUserData(UserData $userData): void
    {
        $this->userData = $userData;
    }

    /**
     * @return \DateTime
     */
    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    /**
     * Solr only deals with utc dates so we set one without modifying the actual date time value.
     *
     * @param \DateTime $lastModified
     */
    public function setLastModified(\DateTime $lastModified): void
    {
        $this->lastModified = $lastModified;
    }

    /**
     * @return int
     */
    public function getSizeInBytes(): int
    {
        return $this->sizeInBytes;
    }

    /**
     * @param int $sizeInBytes
     */
    public function setSizeInBytes(int $sizeInBytes): void
    {
        $this->sizeInBytes = $sizeInBytes;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return array<string, int|bool|string|\DateTime|UserData>
     */
    public function jsonSerialize(): array
    {
        return [
            'numDocs' => $this->numDocs,
            'maxDoc' => $this->maxDoc,
            'deletedDocs' => $this->deletedDocs,
            'indexHeapUsageBytes' => $this->indexHeapUsageBytes,
            'version' => $this->version,
            'segmentCount' => $this->segmentCount,
            'current' => $this->current,
            'hasDeletions' => $this->hasDeletions,
            'directory' => $this->directory,
            'segmentsFile' => $this->segmentsFile,
            'userData' => $this->userData,
            'lastModified' => DateTimeUtil::toSolrString($this->lastModified),
            'sizeInBytes' => $this->sizeInBytes,
            'size' => $this->size,
        ];
    }
}
