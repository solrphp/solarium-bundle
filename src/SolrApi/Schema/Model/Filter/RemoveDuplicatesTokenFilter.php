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
 * Remove Duplicates TokenFilter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class RemoveDuplicatesTokenFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.RemoveDuplicatesTokenFilterFactory';

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
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return ['class' => $this->class];
    }
}
