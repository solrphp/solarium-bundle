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
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Processor\DynamicFieldConfigNodeProcessor;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\DynamicFieldsResponse;

/**
 * DynamicField ConfigNode Processor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DynamicFieldConfigNodeProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testCurrentException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to retrieve current dynamic field config: foo');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'))
        ;

        (new DynamicFieldConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid dynamic field response for sub path bar');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new ConfigResponse())
        ;

        (new DynamicFieldConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testAddFieldCommand(): void
    {
        $field = new Field('foo');
        $secondField = new Field('qux');

        $node = new ConfigNode('foo', SubPath::LIST_DYNAMIC_FIELDS, new ArrayCollection([$field, $secondField]));

        $currentField = new Field('bar');
        $secondCurrentField = new Field('qux');

        $response = new DynamicFieldsResponse();
        $response->addDynamicField($currentField);
        $response->addDynamicField($secondCurrentField);

        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with(SubPath::LIST_DYNAMIC_FIELDS)
            ->willReturn($response);

        $manager->expects(self::exactly(3))
            ->method('addCommand')
            ->withConsecutive(
                [Command::ADD_DYNAMIC_FIELD, $field],
                [Command::REPLACE_DYNAMIC_FIELD, $secondField],
                [Command::DELETE_DYNAMIC_FIELD, $currentField]
            )
        ;

        (new DynamicFieldConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testFailedToCreateOrUpdateField(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to add command for type foo: [error message]');

        $field = new Field('foo');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection([$field]));

        $currentField = new Field('bar');

        $response = new DynamicFieldsResponse();
        $response->addDynamicField($currentField);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(Command::ADD_DYNAMIC_FIELD, $field)
            ->willThrowException(new UnexpectedValueException('[error message]'))
        ;

        (new DynamicFieldConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new ConfigNode(Field::class, SubPath::LIST_DYNAMIC_FIELDS, new ArrayCollection());
        $nodeTwo = new ConfigNode(Field::class, SubPath::LIST_FIELDS, new ArrayCollection());

        self::assertTrue((new DynamicFieldConfigNodeProcessor())->supports($nodeOne));
        self::assertFalse((new DynamicFieldConfigNodeProcessor())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeProcessorInterface::PRIORITY, DynamicFieldConfigNodeProcessor::getDefaultPriority());
    }
}
