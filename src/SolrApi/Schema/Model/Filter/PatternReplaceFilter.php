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
 * Pattern Replace Filter.
 *
 * @author wicliff <wwolda@gmail.com>
 */
final class PatternReplaceFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.PatternReplaceFilterFactory';
    /**
     * @var string
     */
    private string $pattern;
    /**
     * @var string
     */
    private string $replacement;
    /**
     * @var string|null
     */
    private ?string $replace = null;

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
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getReplacement(): string
    {
        return $this->replacement;
    }

    /**
     * @param string $replacement
     */
    public function setReplacement(string $replacement): void
    {
        $this->replacement = $replacement;
    }

    /**
     * @return string|null
     */
    public function getReplace(): ?string
    {
        return $this->replace;
    }

    /**
     * @param string|null $replace
     */
    public function setReplace(?string $replace): void
    {
        $this->replace = $replace;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'pattern' => $this->pattern,
                'replacement' => $this->replacement,
                'replace' => $this->replace,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
