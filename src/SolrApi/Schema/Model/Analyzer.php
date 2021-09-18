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

use Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface;

/**
 * Analyzer.
 *
 * @author wicliff <wwolda@gmail.com>
 */
final class Analyzer implements \JsonSerializable
{
    /**
     * @var string|null
     */
    private ?string $class = null;

    /**
     * @var string|null
     */
    private ?string $type = null;

    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface[]
     */
    private array $charFilters = [];

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Model\Tokenizer|null
     */
    private ?Tokenizer $tokenizer = null;

    /**
     * @var \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface[]
     */
    private array $filters = [];

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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Schema\Model\Tokenizer|null
     */
    public function getTokenizer(): ?Tokenizer
    {
        return $this->tokenizer;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Model\Tokenizer|null $tokenizer
     */
    public function setTokenizer(?Tokenizer $tokenizer): void
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface $filter
     *
     * @return bool
     */
    public function removeFilter(FilterInterface $filter): bool
    {
        return $this->remove($filter, $this->filters);
    }

    /**
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface[]
     */
    public function getCharFilters(): array
    {
        return $this->charFilters;
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface $filter
     */
    public function addCharFilter(FilterInterface $filter): void
    {
        $this->charFilters[] = $filter;
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface $filter
     *
     * @return bool
     */
    public function removeCharFilter(FilterInterface $filter): bool
    {
        return $this->remove($filter, $this->charFilters);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'type' => $this->type,
                'charFilters' => $this->charFilters,
                'tokenizer' => $this->tokenizer,
                'filters' => $this->filters,
            ],
            static function ($val) {
                return null !== $val && (false === \is_array($val) || 0 !== \count($val));
            }
        );
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface $value
     * @param array<int, mixed>                                        $property
     *
     * @return bool
     */
    private function remove(FilterInterface $value, array &$property): bool
    {
        $key = array_search($value, $property, true);

        if (false === $key) {
            return false;
        }

        unset($property[$key]);

        return true;
    }
}
