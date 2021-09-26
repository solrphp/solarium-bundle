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
 * Beider Morse Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class BeiderMorseFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.BeiderMorseFilterFactory';

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $nameType = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $ruleType = null;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private ?string $concat = null;

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
    public function getNameType(): ?string
    {
        return $this->nameType;
    }

    /**
     * @param string|null $nameType
     */
    public function setNameType(?string $nameType): void
    {
        $this->nameType = $nameType;
    }

    /**
     * @return string|null
     */
    public function getRuleType(): ?string
    {
        return $this->ruleType;
    }

    /**
     * @param string|null $ruleType
     */
    public function setRuleType(?string $ruleType): void
    {
        $this->ruleType = $ruleType;
    }

    /**
     * @return string|null
     */
    public function getConcat(): ?string
    {
        return $this->concat;
    }

    /**
     * @param string|null $concat
     */
    public function setConcat(?string $concat): void
    {
        $this->concat = $concat;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'nameType' => $this->nameType,
                'ruleType' => $this->ruleType,
                'concat' => $this->concat,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
