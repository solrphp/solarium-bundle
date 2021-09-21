<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Manager\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Manager\ConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Processor\FieldTypeConfigNodeProcessor;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\FieldTypeResponse;

/**
 * FieldType ConfigNode Processor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class FieldTypeConfigNodeProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testCurrentException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to retrieve current field type config: foo');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'))
        ;

        (new FieldTypeConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid field type response for sub path bar');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new ConfigResponse())
        ;

        (new FieldTypeConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testAddFieldCommand(): void
    {
        $field = new FieldType('foo');
        $secondField = new FieldType('qux');

        $node = new ConfigNode('foo', SubPath::LIST_FIELD_TYPES, new ArrayCollection([$field, $secondField]));

        $currentField = new FieldType('bar');
        $secondCurrentField = new FieldType('qux');

        $response = new FieldTypeResponse();
        $response->addFieldType($currentField);
        $response->addFieldType($secondCurrentField);

        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with(SubPath::LIST_FIELD_TYPES)
            ->willReturn($response);

        $manager->expects(self::exactly(3))
            ->method('addCommand')
            ->withConsecutive(
                [Command::ADD_FIELD_TYPE, $field],
                [Command::REPLACE_FIELD_TYPE, $secondField],
                [Command::DELETE_FIELD_TYPE, $currentField]
            )
        ;

        (new FieldTypeConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testFailedToCreateOrUpdateField(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to add command for type foo: [error message]');

        $field = new FieldType('foo');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection([$field]));

        $currentField = new FieldType('bar');

        $response = new FieldTypeResponse();
        $response->addFieldType($currentField);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(Command::ADD_FIELD_TYPE, $field)
            ->willThrowException(new UnexpectedValueException('[error message]'))
        ;

        (new FieldTypeConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new ConfigNode(FieldType::class, 'foo', new ArrayCollection());
        $nodeTwo = new ConfigNode(Field::class, SubPath::LIST_DYNAMIC_FIELDS, new ArrayCollection());

        self::assertTrue((new FieldTypeConfigNodeProcessor())->supports($nodeOne));
        self::assertFalse((new FieldTypeConfigNodeProcessor())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeProcessorInterface::PRIORITY, FieldTypeConfigNodeProcessor::getDefaultPriority());
    }
}
