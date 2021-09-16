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
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     */
    private array $defaults = [];

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     */
    private array $appends = [];

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
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
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Property $default
     */
    public function addDefault(Property $default): void
    {
        $this->defaults[] = $default;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Property $default
     */
    public function removeDefault(Property $default): void
    {
        if (false === $key = array_search($default, $this->defaults, true)) {
            return;
        }

        unset($this->defaults[$key]);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     */
    public function getAppends(): array
    {
        return $this->appends;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Property $append
     */
    public function addAppend(Property $append): void
    {
        $this->appends[] = $append;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Property $append
     */
    public function removeAppend(Property $append): void
    {
        if (false === $key = array_search($append, $this->appends, true)) {
            return;
        }

        unset($this->appends[$key]);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     */
    public function getInvariants(): array
    {
        return $this->invariants;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Property $invariant
     */
    public function addInvariant(Property $invariant): void
    {
        $this->invariants[] = $invariant;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\Property $invariant
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
     * @param string $component
     */
    public function addComponent(string $component): void
    {
        $this->components[] = $component;
    }

    /**
     * @param string $component
     */
    public function removeComponent(string $component): void
    {
        if (false === $key = array_search($component, $this->components, true)) {
            return;
        }

        unset($this->components[$key]);
    }

    /**
     * @return string[]
     */
    public function getFirstComponents(): array
    {
        return $this->firstComponents;
    }

    /**
     * @param string $component
     */
    public function addFirstComponent(string $component): void
    {
        $this->firstComponents[] = $component;
    }

    /**
     * @param string $component
     */
    public function removeFirstComponent(string $component): void
    {
        if (false === $key = array_search($component, $this->firstComponents, true)) {
            return;
        }

        unset($this->firstComponents[$key]);
    }

    /**
     * @return string[]
     */
    public function getLastComponents(): array
    {
        return $this->lastComponents;
    }

    /**
     * @param string $component
     */
    public function addLastComponent(string $component): void
    {
        $this->lastComponents[] = $component;
    }

    /**
     * @param string $component
     */
    public function removeLastComponent(string $component): void
    {
        if (false === $key = array_search($component, $this->lastComponents, true)) {
            return;
        }

        unset($this->lastComponents[$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
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
     * @param array<int, \Solrphp\SolariumBundle\SolrApi\Config\Model\Property> $properties
     *
     * @return array<string, mixed>|null
     */
    private function map(array $properties): ?array
    {
        if (0 === \count($properties)) {
            return null;
        }

        $return = [];

        /** @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Property $property */
        foreach ($properties as $property) {
            if (null !== ($name = $property->getName()) && null !== ($value = $property->getValue())) {
                $return[$name] = $value;
            }
        }

        return $return;
    }
}
