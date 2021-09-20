<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Config;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;

/**
 * SolrConfig Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrConfigTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSolrConfigConstructor(): void
    {
        $cores = $this->getCores();
        $searchComponents = $this->getSearchComponents();
        $requestHandlers = $this->getRequestHandlers();
        $query = $this->getQuery();

        $solrConfig = new SolrConfig($cores, $searchComponents, $requestHandlers, $query);

        self::assertSame($cores, $solrConfig->getCores());
        self::assertSame($searchComponents, $solrConfig->getSearchComponents());
        self::assertSame($requestHandlers, $solrConfig->getRequestHandlers());
        self::assertSame($query, $solrConfig->getQuery());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSolrConfigAccessor(): void
    {
        $cores = $this->getCores();
        $core = $cores->first();
        $searchComponent = $this->getSearchComponents()->first();
        $requestHandler = $this->getRequestHandlers()->first();
        $query = $this->getQuery();

        $solrConfig = new SolrConfig($cores);

        $solrConfig->addSearchComponent($searchComponent);
        $solrConfig->addRequestHandler($requestHandler);
        $solrConfig->setQuery($query);
        $solrConfig->addCore('qux');

        self::assertContains($core, $solrConfig->getCores());
        self::assertContains('qux', $solrConfig->getCores());
        self::assertContains($searchComponent, $solrConfig->getSearchComponents());
        self::assertContains($requestHandler, $solrConfig->getRequestHandlers());
        self::assertSame($query, $solrConfig->getQuery());

        self::assertTrue($solrConfig->removeCore($core));
        self::assertFalse($solrConfig->removeCore($core));
        self::assertTrue($solrConfig->removeSearchComponent($searchComponent));
        self::assertFalse($solrConfig->removeSearchComponent($searchComponent));
        self::assertTrue($solrConfig->removeRequestHandler($requestHandler));
        self::assertFalse($solrConfig->removeRequestHandler($requestHandler));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOptionalArguments(): void
    {
        $solrConfig = new SolrConfig(new ArrayCollection(['foo']));

        self::assertInstanceOf(ArrayCollection::class, $solrConfig->getRequestHandlers());
        self::assertEmpty($solrConfig->getRequestHandlers());

        self::assertInstanceOf(ArrayCollection::class, $solrConfig->getSearchComponents());
        self::assertEmpty($solrConfig->getSearchComponents());
    }

    /**
     * @return ArrayCollection<int, string>
     */
    private function getCores(): ArrayCollection
    {
        return new ArrayCollection([
            'foo',
            'bar',
            'baz',
        ]);
    }

    /**
     * @return ArrayCollection<int, SearchComponent>
     */
    private function getSearchComponents(): ArrayCollection
    {
        $return = new ArrayCollection();

        for ($i = 0; $i < 3; ++$i) {
            $searchComponent = new SearchComponent();
            $searchComponent->setName('foo');
            $searchComponent->setClass('bar');

            $return->add($searchComponent);
        }

        return $return;
    }

    /**
     * @return ArrayCollection<int, RequestHandler>
     */
    private function getRequestHandlers(): ArrayCollection
    {
        $return = new ArrayCollection();

        for ($i = 0; $i < 3; ++$i) {
            $requestHandler = new RequestHandler();
            $requestHandler->setName('foo');
            $requestHandler->setClass('bar');

            $return->add($requestHandler);
        }

        return $return;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\Query
     */
    private function getQuery(): Query
    {
        $query = new Query();
        $query->setEnableLazyFieldLoading(true);

        return $query;
    }
}
