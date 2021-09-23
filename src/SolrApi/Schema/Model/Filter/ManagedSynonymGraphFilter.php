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
 * Managed Synonym Graph Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ManagedSynonymGraphFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.ManagedStopFilterFactory';

    /**
     * @var string
     */
    private string $managed;

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
    public function getManaged(): string
    {
        return $this->managed;
    }

    /**
     * @param string $managed
     */
    public function setManaged(string $managed): void
    {
        $this->managed = $managed;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => $this->class,
            'managed' => $this->managed,
        ];
    }
}
