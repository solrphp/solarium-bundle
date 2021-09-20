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
 * ASCII Folding Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ASCIIFoldingFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.ASCIIFoldingFilterFactory';

    /**
     * @var bool|null
     */
    private ?bool $preserveOriginal = null;

    /**
     * {@inheritDoc}
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
     * @param bool|null $preserveOriginal
     */
    public function setPreserveOriginal(?bool $preserveOriginal): void
    {
        $this->preserveOriginal = $preserveOriginal;
    }

    /**
     * @return bool|null
     */
    public function getPreserveOriginal(): ?bool
    {
        return $this->preserveOriginal;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'preserveOriginal' => $this->preserveOriginal,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
