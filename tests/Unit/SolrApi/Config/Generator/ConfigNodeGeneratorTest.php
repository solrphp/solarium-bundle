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

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler;

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
        $updateHandler = new UpdateHandler();
        $requestDispatcher = new RequestDispatcher();
        $config = new SolrConfig(['foo'], [$searchComponent], [$requestHandler], $query, $updateHandler, $requestDispatcher);

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
            [
                'type' => UpdateHandler::class,
                'path' => SubPath::GET_UPDATE_HANDLER,
                'first' => $updateHandler,
            ],
            [
                'type' => RequestDispatcher::class,
                'path' => SubPath::GET_REQUEST_DISPATCHER,
                'first' => $requestDispatcher,
            ],
        ];

        foreach ((new ConfigNodeGenerator())->get($config) as $key => $configNode) {
            self::assertSame($result[$key]['type'], $configNode->getType());
            self::assertSame($result[$key]['path'], $configNode->getPath());

            $value = ($configNode instanceof IterableConfigNode) ? $configNode->get()->current() : $configNode->get();

            self::assertSame($result[$key]['first'], $value);
        }

        // making sure all nodes are returned
        self::assertSame(4, $key);
    }
}
