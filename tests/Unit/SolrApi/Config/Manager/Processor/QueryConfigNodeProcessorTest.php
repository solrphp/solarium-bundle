<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Manager\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Manager\ConfigNode;
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\Handler\QueryConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\SchemaResponse;

/**
 * Query ConfigNode Processor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class QueryConfigNodeProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testCurrentException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to retrieve query config for sub path bar');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'))
        ;

        (new QueryConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid query response for sub path bar');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new SchemaResponse())
        ;

        (new QueryConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testSetAndUnsetQueryPropertiesCommand(): void
    {
        $configuredQuery = new Query();
        $configuredQuery->setEnableLazyFieldLoading(true);
        $configuredQuery->setQueryResultMaxDocsCached(10);

        $currentQuery = new Query();
        $currentQuery->setMaxBooleanClauses(1024);
        $currentQuery->setQueryResultMaxDocsCached(20);

        $node = new ConfigNode('foo', 'bar', $configuredQuery);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, $currentQuery);

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::exactly(3))
            ->method('addCommand')
            ->withConsecutive(
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'query.queryResultMaxDocsCached' === $property->getName() && 10 === $property->getValue();
                    }),
                ],
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'query.enableLazyFieldLoading' === $property->getName() && true === $property->getValue();
                    }),
                ],
                [
                    Command::UNSET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'query.maxBooleanClauses' === $property->getName();
                    }),
                ],
            )
        ;

        (new QueryConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testUnsetPropertyException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to unset-property query.maxBooleanClauses');

        $configuredQuery = new Query();
        $configuredQuery->setEnableLazyFieldLoading(true);
        $configuredQuery->setQueryResultMaxDocsCached(10);

        $currentQuery = new Query();
        $currentQuery->setMaxBooleanClauses(1024);
        $currentQuery->setQueryResultMaxDocsCached(20);

        $node = new ConfigNode('foo', 'bar', $configuredQuery);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, $currentQuery);

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::exactly(3))
            ->method('addCommand')
            ->withConsecutive(
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'query.queryResultMaxDocsCached' === $property->getName() && 10 === $property->getValue();
                    }),
                ],
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'query.enableLazyFieldLoading' === $property->getName() && true === $property->getValue();
                    }),
                ],
                [
                    Command::UNSET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'query.maxBooleanClauses' === $property->getName();
                    }),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                $manager,
                $manager,
                self::throwException(new UnexpectedValueException()),
            )
        ;

        (new QueryConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testSetPropertyException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to set-property query.queryResultMaxDocsCached');

        $configuredQuery = new Query();
        $configuredQuery->setEnableLazyFieldLoading(true);
        $configuredQuery->setQueryResultMaxDocsCached(10);

        $currentQuery = new Query();
        $currentQuery->setMaxBooleanClauses(1024);
        $currentQuery->setQueryResultMaxDocsCached(20);

        $node = new ConfigNode('foo', 'bar', $configuredQuery);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, $currentQuery);

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(
                Command::SET_PROPERTY,
                self::callback(static function ($property) {
                    return 'query.queryResultMaxDocsCached' === $property->getName() && 10 === $property->getValue();
                })
            )
            ->will(
                self::throwException(new UnexpectedValueException()),
            )
        ;

        (new QueryConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new IterableConfigNode(Query::class, 'bar', new ArrayCollection());
        $nodeTwo = new IterableConfigNode(RequestHandler::class, 'bar', new ArrayCollection());

        self::assertTrue((new QueryConfigNodeHandler())->supports($nodeOne));
        self::assertFalse((new QueryConfigNodeHandler())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeHandlerInterface::PRIORITY, QueryConfigNodeHandler::getDefaultPriority());
    }
}
