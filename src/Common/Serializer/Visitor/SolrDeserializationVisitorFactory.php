<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer\Visitor;

use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\Factory\DeserializationVisitorFactory;

/**
 * SolrphpDeserializationVisitorFactory.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrDeserializationVisitorFactory implements DeserializationVisitorFactory
{
    private \Closure $closure;

    /**
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @return \JMS\Serializer\Visitor\DeserializationVisitorInterface
     */
    public function getVisitor(): DeserializationVisitorInterface
    {
        return new DeserializationVisitorDecorator(new JsonDeserializationVisitor(), $this->closure);
    }
}
