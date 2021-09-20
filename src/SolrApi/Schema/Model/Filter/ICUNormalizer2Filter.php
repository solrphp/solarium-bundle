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

use Solrphp\SolariumBundle\Contract\SolrApi\FilterInterface;

/**
 * ICU Normalizer 2 Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ICUNormalizer2Filter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.ICUNormalizer2FilterFactory';
    /**
     * @var string
     */
    private string $name = 'nfkc_cf';
    /**
     * @var string
     */
    private string $mode = 'compose';
    /**
     * @var string|null
     */
    private ?string $filter = null;

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
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * @return string|null
     */
    public function getFilter(): ?string
    {
        return $this->filter;
    }

    /**
     * @param string|null $filter
     */
    public function setFilter(?string $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'name' => $this->name,
                'mode' => $this->mode,
                'filter' => $this->filter,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
