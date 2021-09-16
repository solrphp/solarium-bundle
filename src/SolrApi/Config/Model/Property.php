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
 * Property.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Property implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var mixed|null
     */
    private $value;

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
        return array_filter(
            [
                $this->name => $this->value,
            ],
            static function ($v, $k) {
                return null !== $v && null !== $k;
            },
            \ARRAY_FILTER_USE_BOTH
        );
    }
}
