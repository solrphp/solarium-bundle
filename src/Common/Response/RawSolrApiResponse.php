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

use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;

/**
 * Raw SolrApi Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RawSolrApiResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @param ?string $body
     */
    public function __construct(string $body = null)
    {
        $this->body = $body;
    }
}
