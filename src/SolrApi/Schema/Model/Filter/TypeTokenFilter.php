<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Model\Filter;

use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * Type Token Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class TypeTokenFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.TypeTokenFilterFactory';

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $types = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $useWhitelist = null;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $enablePositionIncrements = null;

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
     * @return string|null
     */
    public function getTypes(): ?string
    {
        return $this->types;
    }

    /**
     * @param string|null $types
     */
    public function setTypes(?string $types): void
    {
        $this->types = $types;
    }

    /**
     * @return bool|null
     */
    public function getUseWhitelist(): ?bool
    {
        return $this->useWhitelist;
    }

    /**
     * @param bool|null $useWhitelist
     */
    public function setUseWhitelist(?bool $useWhitelist): void
    {
        $this->useWhitelist = $useWhitelist;
    }

    /**
     * @return bool|null
     */
    public function getEnablePositionIncrements(): ?bool
    {
        return $this->enablePositionIncrements;
    }

    /**
     * @param bool|null $enablePositionIncrements
     */
    public function setEnablePositionIncrements(?bool $enablePositionIncrements): void
    {
        $this->enablePositionIncrements = $enablePositionIncrements;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'types' => $this->types,
                'useWhitelist' => $this->useWhitelist,
                'enablePositionIncrements' => $this->enablePositionIncrements,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
