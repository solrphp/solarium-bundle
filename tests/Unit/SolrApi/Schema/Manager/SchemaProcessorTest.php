<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Handler\CopyFieldConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Handler\FieldConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaProcessor;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldsResponse;
use Solrphp\SolariumBundle\Tests\Helper\ObjectUtil;

/**
 * SchemaProcessor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testProcess()
    {
        $field = ObjectUtil::reflect(new Field());
        $schema = new ManagedSchema('foo', ['foo'], [$field]);

        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();

        $response = new FieldsResponse();
        $response->addField($field);

        $manager->expects(self::once())->method('setCore')->with('foo')->willReturnSelf();
        $manager->expects(self::once())->method('persist')->willReturn(new Result(new Query(), new Response('{}', ['HTTP 200 OK'])));
        $manager->expects(self::once())->method('flush');

        $copyFieldProcessor = $this->getMockBuilder(CopyFieldConfigNodeHandler::class)->getMock();
        $copyFieldProcessor->expects(self::once())->method('supports')->willReturn(false);

        $fieldProcessor = $this->getMockBuilder(FieldConfigNodeHandler::class)->onlyMethods(['handle'])->getMock();
        $fieldProcessor->expects(self::once())->method('handle');

        $processors = new ArrayCollection([
            $copyFieldProcessor,
            $fieldProcessor,
        ]);

        $processor = new SchemaProcessor($processors, $manager);

        $processor
            ->withCore('foo')
            ->withSchema($schema)
            ->process();
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testPersistException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to persist managed schema');

        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('setCore')
            ->with('foo');

        $processors = new ArrayCollection([
            new CopyFieldConfigNodeHandler(),
            new FieldConfigNodeHandler(),
        ]);

        $field = ObjectUtil::reflect(new Field());

        $schema = new ManagedSchema('foo', ['foo'], [$field]);

        $response = new FieldsResponse();
        $response->addField($field);

        $manager->expects(self::once())->method('call')->willReturn($response);
        $manager->expects(self::once())->method('persist')->willThrowException(new \JsonException());
        $manager->expects(self::never())->method('flush');

        $processor = new SchemaProcessor($processors, $manager);

        $processor
            ->withCore('foo')
            ->withSchema($schema)
            ->process();
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testGeneratorAssignment(): void
    {
        $generator = $this->getMockBuilder(SchemaNodeGenerator::class)->getMock();
        $generator->expects(self::once())->method('get');
        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();

        $schema = new ManagedSchema('id', ['foo']);

        $processor = new SchemaProcessor(new ArrayCollection(), $manager, $generator);
        $processor
            ->withCore('foo')
            ->withSchema($schema)
            ->process();
    }
}
