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
 * Length Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class LengthFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.LengthFilterFactory';

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $min;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $max;

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
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax(int $max): void
    {
        $this->max = $max;
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
                'min' => $this->min,
                'max' => $this->max,
                'enablePositionIncrements' => $this->enablePositionIncrements,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
