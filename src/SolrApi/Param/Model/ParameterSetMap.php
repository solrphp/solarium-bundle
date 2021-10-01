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
 * Params.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParameterSetMap implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var array<\Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter>
     *
     * @Serializer\Type("ParameterList")
     */
    private array $parameters = [];

    /**
     * @var array<\Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter>
     *
     * @Serializer\SerializedName("_invariants_")
     * @Serializer\Type("ParameterList")
     */
    private array $invariants = [];

    /**
     * @var array<\Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter>
     *
     * @Serializer\SerializedName("_appends_")
     * @Serializer\Type("ParameterList")
     */
    private array $appends = [];

    /**
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
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
     * @return \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter $parameter
     */
    public function addParameter(Parameter $parameter): void
    {
        $this->parameters[] = $parameter;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter $parameter
     *
     * @return bool
     */
    public function removeParameter(Parameter $parameter): bool
    {
        return $this->remove($parameter, $this->parameters);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter[]
     */
    public function getInvariants(): array
    {
        return $this->invariants;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter $parameter
     */
    public function addInvariant(Parameter $parameter): void
    {
        $this->invariants[] = $parameter;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter $parameter
     *
     * @return bool
     */
    public function removeInvariant(Parameter $parameter): bool
    {
        return $this->remove($parameter, $this->invariants);
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter[]
     */
    public function getAppends(): array
    {
        return $this->appends;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter $parameter
     */
    public function addAppend(Parameter $parameter): void
    {
        $this->appends[] = $parameter;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter $parameter
     *
     * @return bool
     */
    public function removeAppend(Parameter $parameter): bool
    {
        return $this->remove($parameter, $this->appends);
    }

    /**
     * @return array<int|string, array<string, array<mixed>|bool|float|int|string>|string>
     */
    public function jsonSerialize(): array
    {
        $return = [
            $this->name => array_merge(
                $this->map($this->parameters) ?? [],
                array_filter(
                    [
                        '_invariants_' => $this->map($this->invariants),
                        '_appends_' => $this->map($this->appends),
                    ],
                    static function ($val) {
                        return null !== $val && 0 !== \count($val);
                    }
                )
            ),
        ];

        return \count($return[$this->name]) ? $return : [$this->name];
    }

    /**
     * defaults, appends and invariants need to be defined as map when updating solr config.
     *
     * @param array<int, \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter> $parameters
     *
     * @return array<string, array<mixed>|bool|float|int|string>|null
     */
    private function map(array $parameters): ?array
    {
        if (0 === \count($parameters)) {
            return null;
        }

        $return = [];

        /** @var \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter $parameter */
        foreach ($parameters as $parameter) {
            if (null !== ($name = $parameter->getName()) && null !== ($value = $parameter->getValue())) {
                $return[$name] = $value;
            }
        }

        return $return;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter        $value
     * @param array<\Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter> $property
     *
     * @return bool
     */
    private function remove(Parameter $value, array &$property): bool
    {
        $key = array_search($value, $property, true);

        if (false === $key) {
            return false;
        }

        unset($property[$key]);

        return true;
    }
}
