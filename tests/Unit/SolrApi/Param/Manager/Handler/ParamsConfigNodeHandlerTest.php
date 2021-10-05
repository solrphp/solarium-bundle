<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Manager\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Exception\ProcessorException;
use Solrphp\SolariumBundle\Common\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\Common\Manager\ConfigNode;
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Param\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\Handler\ParamsConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamManager;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;

/**
 * ParamsConfigNodeHandlerTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamsConfigNodeHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testCurrentException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to retrieve params config from sub path');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ParamManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'));

        (new ParamsConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid params response for sub path');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ParamManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new ConfigResponse());

        (new ParamsConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidConfigNode(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(sprintf('invalid config node use %s', IterableConfigNode::class));

        $node = new ConfigNode('foo', 'bar', new ParameterSetMap());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::never())->method('call');

        (new ParamsConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Common\Exception\ProcessorException
     */
    public function testAddParamsCommand(): void
    {
        $setMapOne = new ParameterSetMap('foo');
        $setMapTwo = new ParameterSetMap('qux');

        $node = new IterableConfigNode('foo', SubPath::LIST_PARAMS, new ArrayCollection([$setMapOne, $setMapTwo]));

        $currentSetMapOne = new ParameterSetMap('bar');
        $currentSetMapTwo = new ParameterSetMap('qux');

        $response = new ParamResponse();
        $response->addParam($currentSetMapOne);
        $response->addParam($currentSetMapTwo);

        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with(SubPath::LIST_PARAMS)
            ->willReturn($response);

        $manager->expects(self::exactly(3))
            ->method('addCommand')
            ->withConsecutive(
                [Command::SET_PARAM, $setMapOne],
                [Command::UPDATE_PARAM, $setMapTwo],
                [Command::DELETE_PARAM, $currentSetMapOne]
            );

        (new ParamsConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Common\Exception\ProcessorException
     */
    public function testFailedToCreateOrUpdateField(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to add command for type foo: [error message]');

        $setMapOne = new ParameterSetMap('foo');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection([$setMapOne]));

        $setMapTwo = new ParameterSetMap('bar');

        $response = new ParamResponse();
        $response->addParam($setMapTwo);

        $manager = $this->getMockBuilder(ParamManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(Command::SET_PARAM, $setMapOne)
            ->willThrowException(new UnexpectedValueException('[error message]'));

        (new ParamsConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new IterableConfigNode(ParameterSetMap::class, SubPath::LIST_PARAMS, new ArrayCollection());
        $nodeTwo = new IterableConfigNode(Field::class, 'foo', new ArrayCollection());

        self::assertTrue((new ParamsConfigNodeHandler())->supports($nodeOne));
        self::assertFalse((new ParamsConfigNodeHandler())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeHandlerInterface::PRIORITY, ParamsConfigNodeHandler::getDefaultPriority());
    }
}
