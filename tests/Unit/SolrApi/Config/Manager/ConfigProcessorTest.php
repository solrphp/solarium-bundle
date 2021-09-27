<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Server\Api\Query;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigProcessor;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\Handler\RequestHandlerConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\Handler\SearchComponentConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\SearchComponent;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\Tests\Helper\ObjectUtil;

/**
 * ConfigProcessor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testProcess()
    {
        $searchComponent = ObjectUtil::reflect(new SearchComponent());
        $config = new SolrConfig(['foo'], [$searchComponent]);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();

        $response = new ConfigResponse();
        $response->setConfig($config);

        $manager->expects(self::once())->method('setCore')->with('foo')->willReturnSelf();
        $manager->expects(self::once())->method('persist')->willReturn(new Result(new Query(), new Response('', ['HTTP 200 OK'])));
        $manager->expects(self::once())->method('flush');

        $requestProcessor = $this->getMockBuilder(RequestHandlerConfigNodeHandler::class)->getMock();
        $requestProcessor->expects(self::once())->method('supports')->willReturn(false);

        $searchProcessor = $this->getMockBuilder(SearchComponentConfigNodeHandler::class)->onlyMethods(['handle'])->getMock();
        $searchProcessor->expects(self::once())->method('handle');

        $processors = new ArrayCollection([
            $requestProcessor,
            $searchProcessor,
        ]);

        $processor = new ConfigProcessor($processors, $manager);

        $processor
            ->withCore('foo')
            ->withConfig($config)
            ->process();
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testPersistException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to persist configuration');

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('setCore')
            ->with('foo');

        $processors = new ArrayCollection([
            new RequestHandlerConfigNodeHandler(),
            new SearchComponentConfigNodeHandler(),
        ]);

        $searchComponent = ObjectUtil::reflect(new SearchComponent());

        $config = new SolrConfig(['foo'], [$searchComponent]);

        $response = new ConfigResponse();
        $response->setConfig($config);

        $manager->expects(self::once())->method('call')->willReturn($response);
        $manager->expects(self::once())->method('persist')->willThrowException(new \JsonException());
        $manager->expects(self::never())->method('flush');

        $processor = new ConfigProcessor($processors, $manager);

        $processor
            ->withCore('foo')
            ->withConfig($config)
            ->process();
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testGeneratorAssignment(): void
    {
        $generator = $this->getMockBuilder(ConfigNodeGenerator::class)->getMock();
        $generator->expects(self::once())->method('get');
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();

        $config = new SolrConfig(['foo']);

        $processor = new ConfigProcessor(new ArrayCollection(), $manager, $generator);
        $processor
            ->withCore('foo')
            ->withConfig($config)
            ->process();
    }
}
