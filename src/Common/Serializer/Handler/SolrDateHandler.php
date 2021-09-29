<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

/**
 * Solr Date Handler.
 *
 * jms doesn't like this format "2021-09-27T06:43:54.052Z"
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrDateHandler implements SubscribingHandlerInterface
{
    /**
     * @return array<int, array<string, int|string>>
     */
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => \DateTime::class,
                'method' => 'deserializeSolrDate',
            ],
        ];
    }

    /**
     * @param \JMS\Serializer\Visitor\DeserializationVisitorInterface $visitor
     * @param string                                                  $data
     * @param array<string, string|array<mixed>>                      $type
     * @param \JMS\Serializer\Context                                 $context
     *
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function deserializeSolrDate(DeserializationVisitorInterface $visitor, string $data, array $type, Context $context): \DateTime
    {
        return new \DateTime($data);
    }
}
