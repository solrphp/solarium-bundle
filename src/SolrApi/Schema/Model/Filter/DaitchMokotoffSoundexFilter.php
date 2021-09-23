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

use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * Daitch Mokotoff Soundex Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class DaitchMokotoffSoundexFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.DaitchMokotoffSoundexFilterFactory';

    /**
     * @var bool
     */
    private bool $inject = true;

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
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => $this->class,
            'inject' => $this->inject,
        ];
    }
}
