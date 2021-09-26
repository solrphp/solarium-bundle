<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter;

use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * ICU Normalizer2 CharFilter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ICUNormalizer2CharFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.ICUNormalizer2CharFilterFactory';

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $name = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $mode = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getMode(): ?string
    {
        return $this->mode;
    }

    /**
     * @param string|null $mode
     */
    public function setMode(?string $mode): void
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
