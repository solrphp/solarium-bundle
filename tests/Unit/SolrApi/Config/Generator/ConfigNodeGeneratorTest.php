<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;

/**
 * ConfigNode Generator Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigNodeGeneratorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGet(): void
    {
        $searchComponent = new SearchComponent();
        $requestHandler = new RequestHandler();
        $query = new Query();
        $config = new SolrConfig(new ArrayCollection(['foo']), new ArrayCollection([$searchComponent]), new ArrayCollection([$requestHandler]), $query);

        $result = [
            [
                'type' => SearchComponent::class,
                'path' => SubPath::GET_SEARCH_COMPONENTS,
                'first' => $searchComponent,
            ],
            [
                'type' => RequestHandler::class,
                'path' => SubPath::GET_REQUEST_HANDLERS,
                'first' => $requestHandler,
            ],
            [
                'type' => Query::class,
                'path' => SubPath::GET_QUERY,
                'first' => $query,
            ],
        ];

        foreach ((new ConfigNodeGenerator())->get($config) as $key => $configNode) {
            self::assertSame($result[$key]['type'], $configNode->getType());
            self::assertSame($result[$key]['path'], $configNode->getPath());
            foreach ($configNode->get() as $value) {
                break;
            }
            self::assertSame($result[$key]['first'], $value);
        }

        // making sure all nodes are returned
        self::assertSame(2, $key);
    }
}
