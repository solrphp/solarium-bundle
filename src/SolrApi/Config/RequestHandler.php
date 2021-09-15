<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config;

use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Request Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestHandler implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $class;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Property[]
     */
    private array $defaults = [];

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Property[]
     */
    private array $appends = [];

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Property[]
     */
    private array $invariants = [];

    /**
     * @var string[]
     */
    private array $components = [];

    /**
     * @var string[]
     *
     * @Serializer\SerializedName("first-components")
     */
    private array $firstComponents = [];

    /**
     * @var string[]
     *
     * @Serializer\SerializedName("last-components")
     */
    private array $lastComponents = [];

    /**
     * @param string $name
     * @param string $class
     */
    public function __construct(string $name, string $class)
    {
        $this->name = $name;
        $this->class = $class;
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
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Property[]
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Property $default
     */
    public function addDefault(Property $default): void
    {
        $this->defaults[] = $default;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Property $default
     */
    public function removeDefault(Property $default): void
    {
        if (false === $key = array_search($default, $this->defaults, true)) {
            return;
        }

        unset($this->defaults[$key]);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Property[]
     */
    public function getAppends(): array
    {
        return $this->appends;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Property $append
     */
    public function addAppend(Property $append): void
    {
        $this->appends[] = $append;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Property $append
     */
    public function removeAppend(Property $append): void
    {
        if (false === $key = array_search($append, $this->appends, true)) {
            return;
        }

        unset($this->appends[$key]);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Property[]
     */
    public function getInvariants(): array
    {
        return $this->invariants;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Property $invariant
     */
    public function addInvariant(Property $invariant): void
    {
        $this->invariants[] = $invariant;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Property $invariant
     */
    public function removeInvariant(Property $invariant): void
    {
        if (false === $key = array_search($invariant, $this->invariants, true)) {
            return;
        }

        unset($this->invariants[$key]);
    }

    /**
     * @return string[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @param string[] $components
     */
    public function setComponents(array $components): void
    {
        $this->components = $components;
    }

    /**
     * @return string[]
     */
    public function getFirstComponents(): array
    {
        return $this->firstComponents;
    }

    /**
     * @param string[] $firstComponents
     */
    public function setFirstComponents(array $firstComponents): void
    {
        $this->firstComponents = $firstComponents;
    }

    /**
     * @return string[]
     */
    public function getLastComponents(): array
    {
        return $this->lastComponents;
    }

    /**
     * @param string[] $lastComponents
     */
    public function setLastComponents(array $lastComponents): void
    {
        $this->lastComponents = $lastComponents;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter(
            [
                'name' => $this->name,
                'class' => $this->class,
                'defaults' => $this->map($this->defaults),
                'appends' => $this->map($this->appends),
                'invariants' => $this->map($this->invariants),
                'components' => $this->components,
                'first-components' => $this->firstComponents,
                'last-components' => $this->lastComponents,
            ],
            static function ($val) {
                return null !== $val && (false === \is_array($val) || 0 !== \count($val));
            }
        );
    }

    /**
     * defaults, appends and invariants need to be defined as map
     * when updating solr config.
     *
     * @param array<int, \Solrphp\SolariumBundle\SolrApi\Config\Property> $properties
     *
     * @return array<string, mixed>|null
     */
    private function map(array $properties): ?array
    {
        if (0 === \count($properties)) {
            return null;
        }

        $return = [];

        /** @var \Solrphp\SolariumBundle\SolrApi\Config\Property $property */
        foreach ($properties as $property) {
            $return[$property->getName()] = $property->getValue();
        }

        return $return;
    }
}
