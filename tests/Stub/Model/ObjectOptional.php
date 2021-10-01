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

/**
 * ObjectOptional.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ObjectOptional
{
    /**
     * @var string
     */
    private string $foo;
    /**
     * @var string
     */
    private string $bar;

    /**
     * @param string $foo
     * @param string $bar
     */
    public function __construct(string $foo = '', string $bar = '')
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }

    /**
     * @return string
     */
    public function getBar(): string
    {
        return $this->bar;
    }
}
