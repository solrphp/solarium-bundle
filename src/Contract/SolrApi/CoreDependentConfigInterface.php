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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Core Dependent Config Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface CoreDependentConfigInterface
{
    /**
     * @return ArrayCollection<int, string>
     */
    public function getCores(): ArrayCollection;
}
