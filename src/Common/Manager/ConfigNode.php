<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Manager;

use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface;

/**
 * ConfigNode.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigNode implements ConfigNodeInterface
{
    /**
     * @var class-string
     */
    private string $type;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var object
     */
    private object $object;

    /**
     * @param class-string $type
     * @param string       $path
     * @param object       $object
     */
    public function __construct(string $type, string $path, object $object)
    {
        $this->type = $type;
        $this->path = $path;
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return object
     */
    public function get(): object
    {
        return $this->object;
    }
}
