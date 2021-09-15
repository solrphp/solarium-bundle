<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config;

/**
 * Copy Field.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class CopyField implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $source;

    /**
     * @var string
     */
    private string $dest;

    /**
     * @var int|null
     */
    private ?int $maxChars = null;

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getDest(): string
    {
        return $this->dest;
    }

    /**
     * @param string $dest
     */
    public function setDest(string $dest): void
    {
        $this->dest = $dest;
    }

    /**
     * @return int|null
     */
    public function getMaxChars(): ?int
    {
        return $this->maxChars;
    }

    /**
     * @param int|null $maxChars
     */
    public function setMaxChars(?int $maxChars): void
    {
        $this->maxChars = $maxChars;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter(
            [
                'source' => $this->source,
                'dest' => $this->dest,
                'maxChars' => $this->maxChars,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
