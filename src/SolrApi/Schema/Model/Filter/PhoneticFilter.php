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
 * Phonetic Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class PhoneticFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.PhoneticFilterFactory';

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $encoder;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $inject = null;

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
     * @return string
     */
    public function getEncoder(): string
    {
        return $this->encoder;
    }

    /**
     * @param string $encoder
     */
    public function setEncoder(string $encoder): void
    {
        $this->encoder = $encoder;
    }

    /**
     * @return bool|null
     */
    public function getInject(): ?bool
    {
        return $this->inject;
    }

    /**
     * @param bool|null $inject
     */
    public function setInject(?bool $inject): void
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
                'encoder' => $this->encoder,
                'inject' => $this->inject,
                'maxCodeLength' => $this->maxCodeLength,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
