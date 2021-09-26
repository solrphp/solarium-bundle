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
 * Request Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestHandler implements \JsonSerializable
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
    private string $class;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     *
     * @Serializer\Type("PropertyList")
     */
    private array $defaults = [];

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     *
     * @Serializer\Type("PropertyList")
     */
    private array $appends = [];

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\Property[]
     *
     * @Serializer\Type("PropertyList")
     */
    private array $invariants = [];

    /**
     * @var string[]
     *
     * @Serializer\Type("array<string>")
     */
    private array $components = [];

    /**
     * @var string[]
     *
     * @Serializer\Type("array<string>")
     */
    private array $firstComponents = [];

    /**
     * @var string[]
     *
     * @Serializer\Type("array<string>")
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
     *
     * @return bool
     */
    public function removeDefault(Property $default): bool
    {
        return $this->remove($default, $this->defaults);
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
     *
     * @return bool
     */
    public function removeAppend(Property $append): bool
    {
        return $this->remove($append, $this->appends);
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
     *
     * @return bool
     */
    public function removeInvariant(Property $invariant): bool
    {
        return $this->remove($invariant, $this->invariants);
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
     *
     * @return bool
     */
    public function removeComponent(string $component): bool
    {
        return $this->remove($component, $this->components);
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
     *
     * @return bool
     */
    public function removeFirstComponent(string $component): bool
    {
        return $this->remove($component, $this->firstComponents);
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
     *
     * @return bool
     */
    public function removeLastComponent(string $component): bool
    {
        return $this->remove($component, $this->lastComponents);
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

    /**
     * @param string|\Solrphp\SolariumBundle\SolrApi\Config\Model\Property $value
     * @param array<int, mixed>                                            $property
     *
     * @return bool
     */
    private function remove($value, array &$property): bool
    {
        $key = array_search($value, $property, true);

        if (false === $key) {
            return false;
        }

        unset($property[$key]);

        return true;
    }
}
