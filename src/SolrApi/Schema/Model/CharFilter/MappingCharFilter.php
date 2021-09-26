<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Model\CharFilter;

use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * Mapping CharFilter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class MappingCharFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $class = 'solr.MappingCharFilterFactory';

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private string $mapping;

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
    public function getMapping(): string
    {
        return $this->mapping;
    }

    /**
     * @param string $mapping
     */
    public function setMapping(string $mapping): void
    {
        $this->mapping = $mapping;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => $this->class,
            'mapping' => $this->mapping,
        ];
    }
}
