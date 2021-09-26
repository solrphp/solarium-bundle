<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Util;

/**
 * DateTime Util.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DateTimeUtil
{
    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public static function toSolrString(\DateTime $dateTime): string
    {
        $utc = new \DateTime($dateTime->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));

        return sprintf('%s%s', strstr($utc->format(\DateTime::ATOM), '+', true), 'Z');
    }
}
