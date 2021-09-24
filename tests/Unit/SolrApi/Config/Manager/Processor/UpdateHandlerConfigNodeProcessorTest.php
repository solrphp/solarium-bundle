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
use Solrphp\SolariumBundle\Common\Manager\IterableConfigNode;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\Exception\UnexpectedValueException;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Enum\Command;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\Handler\UpdateHandlerConfigNodeHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\AutoSoftCommit;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Query;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Model\UpdateHandler;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;
use Solrphp\SolariumBundle\SolrApi\Schema\Response\SchemaResponse;

/**
 * Query ConfigNode Processor Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UpdateHandlerConfigNodeProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testCurrentException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to retrieve query config for sub path bar');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willThrowException(new UnexpectedValueException('foo'))
        ;

        (new UpdateHandlerConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testInvalidResponse(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('invalid query response for sub path bar');

        $node = new IterableConfigNode('foo', 'bar', new ArrayCollection());
        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->willReturn(new SchemaResponse())
        ;

        (new UpdateHandlerConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testSetAndUnsetQueryPropertiesCommand(): void
    {
        $configuredHandler = new UpdateHandler();
        $configuredHandler->setVersionBucketLockTimeoutMs(1000);
        $configuredHandler->setClass('foo');

        $autoSoftCommit = new AutoSoftCommit();
        $autoSoftCommit->setMaxDocs(10);

        $currentHandler = new UpdateHandler();
        $currentHandler->setVersionBucketLockTimeoutMs(1024);
        $currentHandler->setClass('bar');
        $currentHandler->setAutoSoftCommit($autoSoftCommit);

        $node = new ConfigNode('foo', 'bar', $configuredHandler);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, null, $currentHandler);

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::exactly(3))
            ->method('addCommand')
            ->withConsecutive(
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'updateHandler.class' === $property->getName() && 'foo' === $property->getValue();
                    }),
                ],
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'updateHandler.versionBucketLockTimeoutMs' === $property->getName() && 1000 === $property->getValue();
                    }),
                ],
                [
                    Command::UNSET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'updateHandler.autoSoftCommit.maxDocs' === $property->getName();
                    }),
                ],
            )
        ;

        (new UpdateHandlerConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testUnsetPropertyException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to unset-property updateHandler.autoSoftCommit.maxDocs');

        $configuredHandler = new UpdateHandler();
        $configuredHandler->setVersionBucketLockTimeoutMs(1000);
        $configuredHandler->setClass('foo');

        $autoSoftCommit = new AutoSoftCommit();
        $autoSoftCommit->setMaxDocs(10);

        $currentHandler = new UpdateHandler();
        $currentHandler->setVersionBucketLockTimeoutMs(1024);
        $currentHandler->setClass('bar');
        $currentHandler->setAutoSoftCommit($autoSoftCommit);

        $node = new ConfigNode('foo', 'bar', $configuredHandler);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, null, $currentHandler);

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::exactly(3))
            ->method('addCommand')
            ->withConsecutive(
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'updateHandler.class' === $property->getName() && 'foo' === $property->getValue();
                    }),
                ],
                [
                    Command::SET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'updateHandler.versionBucketLockTimeoutMs' === $property->getName() && 1000 === $property->getValue();
                    }),
                ],
                [
                    Command::UNSET_PROPERTY,
                    self::callback(static function ($property) {
                        return 'updateHandler.autoSoftCommit.maxDocs' === $property->getName();
                    }),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                $manager,
                $manager,
                self::throwException(new UnexpectedValueException()),
            )
        ;

        (new UpdateHandlerConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function testSetPropertyException(): void
    {
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('unable to set-property updateHandler.class');

        $configuredHandler = new UpdateHandler();
        $configuredHandler->setVersionBucketLockTimeoutMs(1000);
        $configuredHandler->setClass('foo');

        $autoSoftCommit = new AutoSoftCommit();
        $autoSoftCommit->setMaxDocs(10);

        $currentHandler = new UpdateHandler();
        $currentHandler->setVersionBucketLockTimeoutMs(1024);
        $currentHandler->setClass('bar');
        $currentHandler->setAutoSoftCommit($autoSoftCommit);

        $node = new ConfigNode('foo', 'bar', $configuredHandler);

        $currentConfig = new SolrConfig(new ArrayCollection(['foo']), null, null, null, $currentHandler);

        $response = new ConfigResponse();
        $response->setConfig($currentConfig);

        $manager = $this->getMockBuilder(ConfigManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(self::once())
            ->method('call')
            ->with('bar')
            ->willReturn($response);

        $manager->expects(self::once())
            ->method('addCommand')
            ->with(
                Command::SET_PROPERTY,
                self::callback(static function ($property) {
                    return 'updateHandler.class' === $property->getName() && 'foo' === $property->getValue();
                })
            )
            ->will(
                self::throwException(new UnexpectedValueException()),
            )
        ;

        (new UpdateHandlerConfigNodeHandler())->setManager($manager)->handle($node);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        $nodeOne = new IterableConfigNode(UpdateHandler::class, 'bar', new ArrayCollection());
        $nodeTwo = new IterableConfigNode(RequestHandler::class, 'bar', new ArrayCollection());

        self::assertTrue((new UpdateHandlerConfigNodeHandler())->supports($nodeOne));
        self::assertFalse((new UpdateHandlerConfigNodeHandler())->supports($nodeTwo));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPriority(): void
    {
        self::assertSame(ConfigNodeHandlerInterface::PRIORITY, UpdateHandlerConfigNodeHandler::getDefaultPriority());
    }
}
