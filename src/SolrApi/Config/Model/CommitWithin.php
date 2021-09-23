<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Model;

/**
 * CommitWithin.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CommitWithin implements \JsonSerializable
{
    /**
     * @var bool
     */
    private bool $softCommit;

    /**
     * @return bool
     */
    public function isSoftCommit(): bool
    {
        return $this->softCommit;
    }

    /**
     * @param bool $softCommit
     */
    public function setSoftCommit(bool $softCommit): void
    {
        $this->softCommit = $softCommit;
    }

    /**
     * @return bool[]
     */
    public function jsonSerialize(): array
    {
        return ['softCommit' => $this->softCommit];
    }
}
