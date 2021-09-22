<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\CoreAdmin\Response;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;

/**
 * StatusResponseTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StatusResponseTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConfigResponseAccessors(): void
    {
        $response = new StatusResponse();
        $status = new Status();

        $response->addStatus($status);

        self::assertContains($status, $response->getStatus());
        self::assertTrue($response->removeStatus($status));
        self::assertFalse($response->removeStatus($status));
    }
}
