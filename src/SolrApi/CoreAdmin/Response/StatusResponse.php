<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response;

use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status;

/**
 * Status Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StatusResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @var array<array-key, Status>
     */
    private array $status = [];

    /**
     * @return Status[]
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status $status
     */
    public function addStatus(Status $status): void
    {
        $this->status[] = $status;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status $status
     *
     * @return bool
     */
    public function removeStatus(Status $status): bool
    {
        $key = array_search($status, $this->status, true);

        if (false === $key) {
            return false;
        }

        unset($this->status[$key]);

        return true;
    }
}
