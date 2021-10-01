<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Parameter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Parameter implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var bool|string|int|float|array<mixed>|null
     *
     * @Serializer\Type("string")
     */
    private $value;

    /**
     * @param string                                  $name
     * @param bool|string|int|float|array<mixed>|null $value
     */
    public function __construct(string $name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

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
     * @return bool|string|int|float|array<mixed>|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool|string|int|float|array<mixed>|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return array<string, bool|string|int|float|array<mixed>|null>
     */
    public function jsonSerialize(): array
    {
        return [$this->name => $this->value];
    }
}
