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
 * Double Metaphone Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class DoubleMetaphoneFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.DoubleMetaphoneFilterFactory';

    /**
     * @var bool
     *
     * @Serializer\Type("bool")
     */
    private bool $inject = true;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     */
    private ?int $maxCodeLength = null;

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
     * @return bool
     */
    public function isInject(): bool
    {
        return $this->inject;
    }

    /**
     * @param bool $inject
     */
    public function setInject(bool $inject): void
    {
        $this->inject = $inject;
    }

    /**
     * @return int|null
     */
    public function getMaxCodeLength(): ?int
    {
        return $this->maxCodeLength;
    }

    /**
     * @param int|null $maxCodeLength
     */
    public function setMaxCodeLength(?int $maxCodeLength): void
    {
        $this->maxCodeLength = $maxCodeLength;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'inject' => $this->inject,
                'maxCodeLength' => $this->maxCodeLength,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
