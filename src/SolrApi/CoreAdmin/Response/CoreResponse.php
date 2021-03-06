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

use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Response\ResponseTrait;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;

/**
 * Core Response.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CoreResponse implements ResponseInterface
{
    use ResponseTrait;
}
