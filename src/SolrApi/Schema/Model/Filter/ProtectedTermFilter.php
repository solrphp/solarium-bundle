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
 * Protected Term Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ProtectedTermFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.ProtectedTermFilterFactory';

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $protected;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $wrappedFilters;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $ignoreCase = null;

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
    public function getProtected(): string
    {
        return $this->protected;
    }

    /**
     * @param string $protected
     */
    public function setProtected(string $protected): void
    {
        $this->protected = $protected;
    }

    /**
     * @return string
     */
    public function getWrappedFilters(): string
    {
        return $this->wrappedFilters;
    }

    /**
     * @param string $wrappedFilters
     */
    public function setWrappedFilters(string $wrappedFilters): void
    {
        $this->wrappedFilters = $wrappedFilters;
    }

    /**
     * @return bool|null
     */
    public function getIgnoreCase(): ?bool
    {
        return $this->ignoreCase;
    }

    /**
     * @param bool|null $ignoreCase
     */
    public function setIgnoreCase(?bool $ignoreCase): void
    {
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'protected' => $this->protected,
                'wrappedFilters' => $this->wrappedFilters,
                'ignoreCase' => $this->ignoreCase,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
