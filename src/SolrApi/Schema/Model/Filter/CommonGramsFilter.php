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
 * Common Grams Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class CommonGramsFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.CommonGramsFilterFactory';

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $words;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $format = null;

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
    public function getWords(): string
    {
        return $this->words;
    }

    /**
     * @param string $words
     */
    public function setWords(string $words): void
    {
        $this->words = $words;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string|null $format
     */
    public function setFormat(?string $format): void
    {
        $this->format = $format;
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
                'words' => $this->words,
                'format' => $this->format,
                'ignoreCase' => $this->ignoreCase,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
