<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Generator;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Param\Generator\ParamsNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;

/**
 * ParamsNodeGeneratorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamsNodeGeneratorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGet(): void
    {
        $setMap = new ParameterSetMap();
        $parameters = new RequestParameters(['foo'], [$setMap, $setMap]);

        $result = [
            [
                'type' => ParameterSetMap::class,
                'path' => SubPath::LIST_PARAMS,
                'first' => $setMap,
            ],
        ];

        foreach ((new ParamsNodeGenerator())->get($parameters) as $key => $paramNode) {
            self::assertSame($result[$key]['type'], $paramNode->getType());
            self::assertSame($result[$key]['path'], $paramNode->getPath());

            $value = $paramNode->get()->current();

            self::assertSame($result[$key]['first'], $value);
        }
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testEmptyConfig(): void
    {
        $parameters = new RequestParameters(['foo'], []);

        self::assertCount(0, (new ParamsNodeGenerator())->get($parameters));
    }
}
