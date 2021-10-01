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
 * Property.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Property implements \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $name;

    /**
     * @var mixed
     *
     * @Serializer\Type("string")
     */
    private $value;

    /**
     * @param string $name
     * @param mixed  $value
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [$this->name => (string) $this->value];
    }
}
