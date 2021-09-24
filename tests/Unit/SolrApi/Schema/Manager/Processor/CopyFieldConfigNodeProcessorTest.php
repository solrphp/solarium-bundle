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
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\Handler\CopyFieldConfigNodeHandler;
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

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'));

        (new CopyFieldConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid field type response for sub path bar');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(SchemaManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new ConfigResponse());

        (new CopyFieldConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidConfigNode(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(sprintf('invalid config node use %s', IterableConfigNode::class));

        $node = new ConfigNode('foo', 'bar', new UpdateHandler());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::never())->method('call');

        (new CopyFieldConfigNodeHandler())->setManager($manager)->handle($node);
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

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection([$field]));

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
            );

        (new CopyFieldConfigNodeHandler())->setManager($manager)->handle($node);
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

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection([$field]));

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
            ->willThrowException(new UnexpectedValueException('[error message]'));

        (new CopyFieldConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new IterableConfigNode(CopyField::class, 'bar', new ArrayCollection());
        $nodeTwo = new IterableConfigNode(Field::class, 'bar', new ArrayCollection());

        self::assertTrue((new CopyFieldConfigNodeHandler())->supports($nodeOne));
        self::assertFalse((new CopyFieldConfigNodeHandler())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeHandlerInterface::PRIORITY, CopyFieldConfigNodeHandler::getDefaultPriority());
    }
}
