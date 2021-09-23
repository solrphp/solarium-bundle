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
 * UpdateHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UpdateHandler implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $class;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\AutoCommit|null
     */
    private ?AutoCommit $autoCommit = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\AutoSoftCommit|null
     */
    private ?AutoSoftCommit $autoSoftCommit = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\CommitWithin|null
     */
    private ?CommitWithin $commitWithin = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateLog|null
     */
    private ?UpdateLog $updateLog = null;

    /**
     * @var int|null
     */
    private ?int $versionBucketLockTimeoutMs = null;

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\AutoCommit|null
     */
    public function getAutoCommit(): ?AutoCommit
    {
        return $this->autoCommit;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\AutoCommit|null $autoCommit
     */
    public function setAutoCommit(?AutoCommit $autoCommit): void
    {
        $this->autoCommit = $autoCommit;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\AutoSoftCommit|null
     */
    public function getAutoSoftCommit(): ?AutoSoftCommit
    {
        return $this->autoSoftCommit;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\AutoSoftCommit|null $autoSoftCommit
     */
    public function setAutoSoftCommit(?AutoSoftCommit $autoSoftCommit): void
    {
        $this->autoSoftCommit = $autoSoftCommit;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\CommitWithin|null
     */
    public function getCommitWithin(): ?CommitWithin
    {
        return $this->commitWithin;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\CommitWithin|null $commitWithin
     */
    public function setCommitWithin(?CommitWithin $commitWithin): void
    {
        $this->commitWithin = $commitWithin;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateLog|null
     */
    public function getUpdateLog(): ?UpdateLog
    {
        return $this->updateLog;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateLog|null $updateLog
     */
    public function setUpdateLog(?UpdateLog $updateLog): void
    {
        $this->updateLog = $updateLog;
    }

    /**
     * @return int|null
     */
    public function getVersionBucketLockTimeoutMs(): ?int
    {
        return $this->versionBucketLockTimeoutMs;
    }

    /**
     * @param int|null $versionBucketLockTimeoutMs
     */
    public function setVersionBucketLockTimeoutMs(?int $versionBucketLockTimeoutMs): void
    {
        $this->versionBucketLockTimeoutMs = $versionBucketLockTimeoutMs;
    }

    /**
     * @return array<string, string|int|AutoCommit|AutoSoftCommit|CommitWithin|UpdateLog>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'autoCommit' => $this->autoCommit,
                'autoSoftCommit' => $this->autoSoftCommit,
                'commitWithin' => $this->commitWithin,
                'updateLog' => $this->updateLog,
                'versionBucketLockTimeoutMs' => $this->versionBucketLockTimeoutMs,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
