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
 * ICU Folding Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ICUFoldingFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.ICUFoldingFilterFactory';

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
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'filter' => $this->filter,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
