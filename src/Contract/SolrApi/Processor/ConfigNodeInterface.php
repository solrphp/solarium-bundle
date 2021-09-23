<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\SolrApi\Processor;

/**
 * ConfigNode Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ConfigNodeInterface
{
    /**
     * returns the node type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * returns the api endpoint for retrieving the current configuration.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * returns (collection of) currently configured node element(s).
     *
     * @return \Generator<int, mixed>|object
     */
    public function get();
}
