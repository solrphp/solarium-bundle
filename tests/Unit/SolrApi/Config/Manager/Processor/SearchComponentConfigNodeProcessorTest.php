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
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\Processor\SearchComponentConfigNodeProcessor;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\SchemaResponse;

/**
 * SearchComponent ConfigNode Processor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SearchComponentConfigNodeProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testCurrentException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to retrieve search component config for sub path bar');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'))
        ;

        (new SearchComponentConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid response for sub path bar');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new SchemaResponse())
        ;

        (new SearchComponentConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testAddRequestHandlerCommand(): void
    {
        $requestHandler = new RequestHandler();
        $requestHandler->setName('foo');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection([$requestHandler]));

        $currentHandler = new RequestHandler();
        $currentHandler->setName('bar');
        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), new ArrayCollection([$currentHandler]));

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(Command::ADD_SEARCH_COMPONENT, $requestHandler)
        ;

        (new SearchComponentConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testUpdateRequestHandlerCommand(): void
    {
        $requestHandler = new RequestHandler();
        $requestHandler->setName('foo');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection([$requestHandler]));

        $currentHandler = new RequestHandler();
        $currentHandler->setName('foo');
        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), new ArrayCollection([$currentHandler]));

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(Command::UPDATE_SEARCH_COMPONENT, $requestHandler)
        ;

        (new SearchComponentConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testFailedToCreateOrUpdateRequestHandler(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(sprintf('unable to add command %s for type foo', Command::UPDATE_SEARCH_COMPONENT));

        $requestHandler = new RequestHandler();
        $requestHandler->setName('foo');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection([$requestHandler]));
        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), new ArrayCollection([$requestHandler]));

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(Command::UPDATE_SEARCH_COMPONENT, $requestHandler)
            ->willThrowException(new UnexpectedValueException())
        ;

        (new SearchComponentConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new ConfigNode(SearchComponent::class, 'bar', new ArrayCollection());
        $nodeTwo = new ConfigNode(RequestHandler::class, 'bar', new ArrayCollection());

        self::assertTrue((new SearchComponentConfigNodeProcessor())->supports($nodeOne));
        self::assertFalse((new SearchComponentConfigNodeProcessor())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeProcessorInterface::PRIORITY, SearchComponentConfigNodeProcessor::getDefaultPriority());
    }
}
