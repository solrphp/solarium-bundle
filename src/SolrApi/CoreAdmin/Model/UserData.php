<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model;

/**
 * User Data.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UserData implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $commitCommandVer;

    /**
     * @var string
     */
    private string $commitTimeMSec;

    /**
     * @return string
     */
    public function getCommitCommandVer(): string
    {
        return $this->commitCommandVer;
    }

    /**
     * @param string $commitCommandVer
     */
    public function setCommitCommandVer(string $commitCommandVer): void
    {
        $this->commitCommandVer = $commitCommandVer;
    }

    /**
     * @return string
     */
    public function getCommitTimeMSec(): string
    {
        return $this->commitTimeMSec;
    }

    /**
     * @param string $commitTimeMSec
     */
    public function setCommitTimeMSec(string $commitTimeMSec): void
    {
        $this->commitTimeMSec = $commitTimeMSec;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'commitCommandVer' => $this->commitCommandVer,
            'commitTimeMSec' => $this->commitTimeMSec,
        ];
    }
}
