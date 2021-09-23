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
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\Processor\RequestDispatcherConfigNodeProcessor;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestDispatcher;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestParser;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\SchemaResponse;

/**
 * Query ConfigNode Processor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestDispatcherConfigNodeProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testCurrentException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to retrieve request dispatcher config for sub path bar');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'))
        ;

        (new RequestDispatcherConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid request dispatcher response for sub path bar');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new SchemaResponse())
        ;

        (new RequestDispatcherConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testSetAndUnsetQueryPropertiesCommand(): void
    {
        $requestDispatcher = new RequestDispatcher();

        $parser = new RequestParser();
        $parser->setAddHttpRequestToContext(true);
        $parser->setEnableRemoteStreaming(false);

        $requestDispatcher->setRequestParsers($parser);

        $configuredParser = new RequestParser();
        $configuredParser->setAddHttpRequestToContext(false);
        $configuredParser->setEnableStreamBody(true);

        $configuredDispatcher = new RequestDispatcher();
        $configuredDispatcher->setRequestParsers($configuredParser);

        $node = new ConfigNode('foo', 'bar', $requestDispatcher);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, null, null, $configuredDispatcher);

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
                        return 'requestDispatcher.requestParsers.enableRemoteStreaming' === $property->getName() && false === $property->getValue();
                    }),
                ],
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'requestDispatcher.requestParsers.addHttpRequestToContext' === $property->getName() && true === $property->getValue();
                    }),
                ],
                [
                    Command::UNSET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'requestDispatcher.requestParsers.enableStreamBody' === $property->getName();
                    }),
                ],
            )
        ;

        (new RequestDispatcherConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testUnsetPropertyException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to unset-property requestDispatcher.requestParsers.enableStreamBody');

        $requestDispatcher = new RequestDispatcher();

        $parser = new RequestParser();
        $parser->setAddHttpRequestToContext(true);
        $parser->setEnableRemoteStreaming(false);

        $requestDispatcher->setRequestParsers($parser);

        $currentParser = new RequestParser();
        $currentParser->setAddHttpRequestToContext(false);
        $currentParser->setEnableStreamBody(true);

        $currentDispatcher = new RequestDispatcher();
        $currentDispatcher->setRequestParsers($currentParser);

        $node = new ConfigNode('foo', 'bar', $requestDispatcher);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, null, null, $currentDispatcher);

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
                        return 'requestDispatcher.requestParsers.enableRemoteStreaming' === $property->getName() && false === $property->getValue();
                    }),
                ],
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'requestDispatcher.requestParsers.addHttpRequestToContext' === $property->getName() && true === $property->getValue();
                    }),
                ],
                [
                    Command::UNSET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'requestDispatcher.requestParsers.enableStreamBody' === $property->getName();
                    }),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                $manager,
                $manager,
                self::throwException(new UnexpectedValueException()),
            )
        ;

        (new RequestDispatcherConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testSetPropertyException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to set-property requestDispatcher.requestParsers.enableRemoteStreaming');

        $requestDispatcher = new RequestDispatcher();

        $parser = new RequestParser();
        $parser->setAddHttpRequestToContext(true);
        $parser->setEnableRemoteStreaming(false);

        $requestDispatcher->setRequestParsers($parser);

        $currentParser = new RequestParser();
        $currentParser->setAddHttpRequestToContext(false);
        $currentParser->setEnableStreamBody(true);

        $currentDispatcher = new RequestDispatcher();
        $currentDispatcher->setRequestParsers($currentParser);

        $node = new ConfigNode('foo', 'bar', $requestDispatcher);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, null, null, $currentDispatcher);

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
                    return 'requestDispatcher.requestParsers.enableRemoteStreaming' === $property->getName() && false === $property->getValue();
                })
            )
            ->will(
                self::throwException(new UnexpectedValueException()),
            )
        ;

        (new RequestDispatcherConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new IterableConfigNode(RequestDispatcher::class, 'bar', new ArrayCollection());
        $nodeTwo = new IterableConfigNode(RequestHandler::class, 'bar', new ArrayCollection());

        self::assertTrue((new RequestDispatcherConfigNodeProcessor())->supports($nodeOne));
        self::assertFalse((new RequestDispatcherConfigNodeProcessor())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeProcessorInterface::PRIORITY, RequestDispatcherConfigNodeProcessor::getDefaultPriority());
    }
}
