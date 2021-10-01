<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters;
use Solrphp\SolariumBundle\SolrApi\Param\Generator\ParamsNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\Handler\ParamsConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamManager;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamProcessor;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Handler\CopyFieldConfigNodeHandler;
use Solrphp\SolariumBundle\Tests\Helper\ObjectUtil;

/**
 * ParamProcessorTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testProcess()
    {
        $setMap = ObjectUtil::reflect(new ParameterSetMap());
        $requestParameters = new RequestParameters(['foo'], [$setMap]);

        $manager = $this->getMockBuilder(ParamManager::class)->disableOriginalConstructor()->getMock();

        $response = new ParamResponse();
        $response->addParam($setMap);

        $manager->expects(self::once())->method('setCore')->with('foo')->willReturnSelf();
        $manager->expects(self::once())->method('persist')->willReturn(new Result(new Query(), new Response('{}', ['HTTP 200 OK'])));
        $manager->expects(self::once())->method('flush');

        $paramsConfigNodeHandler = $this->getMockBuilder(ParamsConfigNodeHandler::class)->getMock();
        $paramsConfigNodeHandler->expects(self::once())->method('supports')->willReturn(true);
        $paramsConfigNodeHandler->expects(self::once())->method('setManager')->willReturnSelf();
        $paramsConfigNodeHandler->expects(self::once())->method('handle');

        $copyFieldConfigNodeHandler = $this->getMockBuilder(CopyFieldConfigNodeHandler::class)->getMock();
        $copyFieldConfigNodeHandler->expects(self::once())->method('supports')->willReturn(false);
        $copyFieldConfigNodeHandler->expects(self::never())->method('handle');

        $handlers = new ArrayCollection([
            $copyFieldConfigNodeHandler,
            $paramsConfigNodeHandler,
        ]);

        $processor = new ParamProcessor($handlers, $manager);

        $processor
            ->withCore('foo')
            ->withRequestParameters($requestParameters)
            ->process();
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testPersistException()
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to persist parameters');

        $setMap = ObjectUtil::reflect(new ParameterSetMap());
        $requestParameters = new RequestParameters(['foo'], [$setMap]);

        $manager = $this->getMockBuilder(ParamManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('setCore')
            ->with('foo');

        $response = new ParamResponse();
        $response->addParam($setMap);

        $manager->expects(self::once())->method('call')->willReturn($response);
        $manager->expects(self::once())->method('persist')->willThrowException(new \JsonException());
        $manager->expects(self::never())->method('flush');

        $handlers = new ArrayCollection([
            new ParamsConfigNodeHandler(),
        ]);

        $processor = new ParamProcessor($handlers, $manager);

        $processor
            ->withCore('foo')
            ->withRequestParameters($requestParameters)
            ->process();
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testGeneratorAssignment(): void
    {
        $generator = $this->getMockBuilder(ParamsNodeGenerator::class)->getMock();
        $generator->expects(self::once())->method('get');
        $manager = $this->getMockBuilder(ParamManager::class)->disableOriginalConstructor()->getMock();

        $requestParameters = new RequestParameters([], []);

        $processor = new ParamProcessor(new ArrayCollection(), $manager, $generator);
        $processor
            ->withCore('foo')
            ->withRequestParameters($requestParameters)
            ->process();
    }
}
