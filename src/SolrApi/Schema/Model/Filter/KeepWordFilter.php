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
 * Keep Word Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class KeepWordFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.KeepWordFilterFactory';

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $words;

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $ignoreCase = null;

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
                'words' => $this->words,
                'ignoreCase' => $this->ignoreCase,
                'enablePositionIncrements' => $this->enablePositionIncrements,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
