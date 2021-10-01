<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Config;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;

/**
 * RequestParametersTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestParametersTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConstructorArguments(): void
    {
        $cores = ['foo'];
        $setMaps = $this->getParameterSetMaps();
        $requestParameters = new RequestParameters($cores, $setMaps);

        self::assertSame($cores, $requestParameters->getCores()->toArray());
        self::assertSame($setMaps, $requestParameters->getParameterSetMaps()->toArray());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRequestParametersAccessors(): void
    {
        $cores = ['foo'];
        $setMap = $this->getParameterSetMaps()[0];

        $requestParameters = new RequestParameters($cores);
        $requestParameters->addCore('qux');
        $requestParameters->addParameterSetMap($setMap);

        self::assertContains('foo', $requestParameters->getCores());
        self::assertContains('qux', $requestParameters->getCores());
        self::assertContains($setMap, $requestParameters->getParameterSetMaps());

        self::assertTrue($requestParameters->removeParameterSetMap($setMap));
        self::assertFalse($requestParameters->removeParameterSetMap($setMap));
        self::assertTrue($requestParameters->removeCore('foo'));
        self::assertFalse($requestParameters->removeCore('foo'));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOptionalArguments(): void
    {
        $requestParameters = new RequestParameters();

        self::assertInstanceOf(ArrayCollection::class, $requestParameters->getCores());
        self::assertEmpty($requestParameters->getCores());
        self::assertInstanceOf(ArrayCollection::class, $requestParameters->getParameterSetMaps());
        self::assertEmpty($requestParameters->getParameterSetMaps());
    }

    /**
     * @return array
     */
    private function getParameterSetMaps(): array
    {
        $return = [];

        for ($i = 0; $i < 3; ++$i) {
            $setMap = new ParameterSetMap();
            $setMap->setName('foo');

            $return[] = $setMap;
        }

        return $return;
    }
}
