<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Response;

use JMS\Serializer\Annotation as Serializer;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseHeaderInterface;

/**
 * Header.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Header implements ResponseHeaderInterface
{
    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $status;

    /**
     * @var int
     *
     * @Serializer\Type("int")
     */
    private int $qTime;

    /**
     * {@inheritdoc}
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getQTime(): int
    {
        return $this->qTime;
    }

    /**
     * @param int $qTime
     */
    public function setQTime(int $qTime): void
    {
        $this->qTime = $qTime;
    }
}
