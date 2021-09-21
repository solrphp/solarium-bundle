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
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Processor\CopyFieldConfigNodeProcessor;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\CopyFieldsResponse;

/**
 * CopyField ConfigNode Processor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CopyFieldConfigNodeProcessorTest extends TestCase
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

        (new CopyFieldConfigNodeProcessor())->setManager($manager)->process($node);
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

        (new CopyFieldConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testAddFieldCommand(): void
    {
        $field = new CopyField();
        $field->setSource('foo');
        $field->setDest('bar');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection([$field]));

        $currentField = new CopyField();
        $currentField->setSource('bar');
        $currentField->setDest('foo');
        //$currentSchema = new ManagedSchema('foo', new ArrayCollection(['foo']), new ArrayCollection([$currentField]));

        $response = new CopyFieldsResponse();
        $response->addCopyField($currentField);

        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::exactly(2))
            ->method('addCommand')
            ->withConsecutive(
                [Command::ADD_COPY_FIELD, $field],
                [Command::DELETE_COPY_FIELD, $currentField]
            )
        ;

        (new CopyFieldConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testFailedToCreateOrUpdateField(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to add command for type foo: [error message]');

        $field = new CopyField();
        $field->setSource('foo');
        $field->setDest('bar');

        $node = new ConfigNode('foo', 'bar', new ArrayCollection([$field]));

        $currentField = new CopyField();
        $currentField->setSource('bar');
        $currentField->setDest('foo');

        $response = new CopyFieldsResponse();
        $response->addCopyField($currentField);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(Command::ADD_COPY_FIELD, $field)
            ->willThrowException(new UnexpectedValueException('[error message]'))
        ;

        (new CopyFieldConfigNodeProcessor())->setManager($manager)->process($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new ConfigNode(CopyField::class, 'bar', new ArrayCollection());
        $nodeTwo = new ConfigNode(Field::class, 'bar', new ArrayCollection());

        self::assertTrue((new CopyFieldConfigNodeProcessor())->supports($nodeOne));
        self::assertFalse((new CopyFieldConfigNodeProcessor())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeProcessorInterface::PRIORITY, CopyFieldConfigNodeProcessor::getDefaultPriority());
    }
}
