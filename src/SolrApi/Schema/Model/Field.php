<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Model;

/**
 * Field.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Field implements \JsonSerializable
{
    use FieldPropertyTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string|null
     */
    private ?string $default = null;

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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @param string|null $default
     */
    public function setDefault(?string $default): void
    {
        $this->default = $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'name' => $this->name,
                'type' => $this->type,
                'default' => $this->default,
                'indexed' => $this->indexed,
                'stored' => $this->stored,
                'docValues' => $this->docValues,
                'sortMissingFirst' => $this->sortMissingFirst,
                'sortMissingLast' => $this->sortMissingLast,
                'multiValued' => $this->multiValued,
                'uninvertible' => $this->uninvertible,
                'omitNorms' => $this->omitNorms,
                'omitTermFreqAndPositions' => $this->omitTermFreqAndPositions,
                'omitPositions' => $this->omitPositions,
                'termVectors' => $this->termVectors,
                'termPositions' => $this->termPositions,
                'termOffsets' => $this->termOffsets,
                'termPayloads' => $this->termPayloads,
                'required' => $this->required,
                'useDocValuesAsStored' => $this->useDocValuesAsStored,
                'large' => $this->large,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
