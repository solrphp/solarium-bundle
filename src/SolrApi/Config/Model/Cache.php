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
 * Cache.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Cache implements \JsonSerializable
{
    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $autowarmCount = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $size = null;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $initialSize = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $class = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $name = null;

    /**
     * @return string|null
     */
    public function getAutowarmCount(): ?string
    {
        return $this->autowarmCount;
    }

    /**
     * @param string|null $autowarmCount
     */
    public function setAutowarmCount(?string $autowarmCount): void
    {
        $this->autowarmCount = $autowarmCount;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     */
    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return int|null
     */
    public function getInitialSize(): ?int
    {
        return $this->initialSize;
    }

    /**
     * @param int|null $initialSize
     */
    public function setInitialSize(?int $initialSize): void
    {
        $this->initialSize = $initialSize;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string|null $class
     */
    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'autowarmCount' => $this->autowarmCount,
                'size' => $this->size,
                'initialSize' => $this->initialSize,
                'class' => $this->class,
                'name' => $this->name,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
