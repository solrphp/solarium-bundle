<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Response;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;

/**
 * ParamResponseTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamResponseTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConfigResponseAccessors(): void
    {
        $response = new ParamResponse();
        $setMap = new ParameterSetMap('foo');

        $response->addParam($setMap);

        self::assertContains($setMap, $response->getParams());
        self::assertTrue($response->removeParam($setMap));
        self::assertFalse($response->removeParam($setMap));
    }
}
