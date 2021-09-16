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
 * Fingerprint Filter.
 *
 * @author wicliff <wwolda@gmail.com>
 */
final class FingerprintFilter implements FilterInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private string $class = 'solr.FingerprintFilterFactory';
    /**
     * @var string|null
     */
    private ?string $separator = null;
    /**
     * @var int|null
     */
    private ?int $maxOutputTokenSize = null;

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
    public function getSeparator(): ?string
    {
        return $this->separator;
    }

    /**
     * @param string|null $separator
     */
    public function setSeparator(?string $separator): void
    {
        $this->separator = $separator;
    }

    /**
     * @return int|null
     */
    public function getMaxOutputTokenSize(): ?int
    {
        return $this->maxOutputTokenSize;
    }

    /**
     * @param int|null $maxOutputTokenSize
     */
    public function setMaxOutputTokenSize(?int $maxOutputTokenSize): void
    {
        $this->maxOutputTokenSize = $maxOutputTokenSize;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'class' => $this->class,
                'separator' => $this->separator,
                'maxOutputTokenSize' => $this->maxOutputTokenSize,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
