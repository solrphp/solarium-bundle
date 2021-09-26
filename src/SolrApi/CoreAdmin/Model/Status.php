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
 * Status.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Status implements \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $name;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $instanceDir;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $dataDir;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $config;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $schema;

    /**
     * @var \DateTime
     *
     * @Serializer\Type("DateTime")
     * @Serializer\Accessor(setter="setStartTime")
     */
    private \DateTime $startTime;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $uptime;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Index|null
     */
    private ?Index $index = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getInstanceDir(): string
    {
        return $this->instanceDir;
    }

    /**
     * @param string $instanceDir
     */
    public function setInstanceDir(string $instanceDir): void
    {
        $this->instanceDir = $instanceDir;
    }

    /**
     * @return string
     */
    public function getDataDir(): string
    {
        return $this->dataDir;
    }

    /**
     * @param string $dataDir
     */
    public function setDataDir(string $dataDir): void
    {
        $this->dataDir = $dataDir;
    }

    /**
     * @return string
     */
    public function getConfig(): string
    {
        return $this->config;
    }

    /**
     * @param string $config
     */
    public function setConfig(string $config): void
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @param string $schema
     */
    public function setSchema(string $schema): void
    {
        $this->schema = $schema;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * Solr only deals with utc dates so we set one without modifying the actual date time value.
     *
     * @param \DateTime $startTime
     */
    public function setStartTime(\DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return int
     */
    public function getUptime(): int
    {
        return $this->uptime;
    }

    /**
     * @param int $uptime
     */
    public function setUptime(int $uptime): void
    {
        $this->uptime = $uptime;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Index|null
     */
    public function getIndex(): ?Index
    {
        return $this->index;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Index|null $index
     */
    public function setIndex(?Index $index): void
    {
        $this->index = $index;
    }

    /**
     * @return array<string, string|int|\DateTime|Index>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'name' => $this->name,
                'instanceDir' => $this->instanceDir,
                'dataDir' => $this->dataDir,
                'config' => $this->config,
                'schema' => $this->schema,
                'startTime' => DateTimeUtil::toSolrString($this->startTime),
                'uptime' => $this->uptime,
                'index' => $this->index,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
