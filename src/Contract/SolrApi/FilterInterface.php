<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\SolrApi;

/**
 * FilterClassInterface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * discriminator map is loaded from a xml mapping file.
 *
 * @see 'Resources\serializer\discriminator-map.xml'
 */
interface FilterInterface
{
    /**
     * @return string
     */
    public function getClass(): string;
}
