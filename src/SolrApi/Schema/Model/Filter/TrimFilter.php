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
 * Trim Filter.
 *
 * @author wicliff <wwolda@gmail.com>
 */
final class TrimFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.TrimFilterFactory';
    /**
     * @var bool|null
     */
    private ?bool $updateOffsets = null;

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
     * @return bool|null
     */
    public function getUpdateOffsets(): ?bool
    {
        return $this->updateOffsets;
    }

    /**
     * @param bool|null $updateOffsets
     */
    public function setUpdateOffsets(?bool $updateOffsets): void
    {
        $this->updateOffsets = $updateOffsets;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'updateOffsets' => $this->updateOffsets,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
