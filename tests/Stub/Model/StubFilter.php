<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Stub\Model;

use Solrphp\SolariumBundle\SolrApi\Schema\Contract\FilterInterface;

/**
 * Stub Filter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StubFilter implements FilterInterface, \JsonSerializable
{
    private string $class = 'foo.bar';

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
     * @return string<string, string>
     */
    public function jsonSerialize(): array
    {
        return ['class' => $this->class];
    }
}
