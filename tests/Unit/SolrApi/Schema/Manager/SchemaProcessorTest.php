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
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Processor\CopyFieldConfigNodeProcessor;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Processor\FieldConfigNodeProcessor;
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
        $schema = new ManagedSchema('foo', new ArrayCollection(['foo']), new ArrayCollection([$field]));

        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();

        $response = new FieldsResponse();
        $response->addField($field);

        $manager->expects(self::once())->method('setCore')->with('foo')->willReturnSelf();
        $manager->expects(self::once())->method('persist');
        $manager->expects(self::once())->method('flush');

        $copyFieldProcessor = $this->getMockBuilder(CopyFieldConfigNodeProcessor::class)->getMock();
        $copyFieldProcessor->expects(self::once())->method('supports')->willReturn(false);

        $fieldProcessor = $this->getMockBuilder(FieldConfigNodeProcessor::class)->onlyMethods(['process'])->getMock();
        $fieldProcessor->expects(self::once())->method('process');

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
            new CopyFieldConfigNodeProcessor(),
            new FieldConfigNodeProcessor(),
        ]);

        $field = ObjectUtil::reflect(new Field());

        $schema = new ManagedSchema('foo', new ArrayCollection(['foo']), new ArrayCollection([$field]));

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

        $schema = new ManagedSchema('id', new ArrayCollection(['foo']));

        $processor = new SchemaProcessor(new ArrayCollection(), $manager, $generator);
        $processor
            ->withCore('foo')
            ->withSchema($schema)
            ->process();
    }
}
