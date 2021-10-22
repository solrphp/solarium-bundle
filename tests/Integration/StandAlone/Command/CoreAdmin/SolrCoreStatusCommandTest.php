<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Integration\StandAlone\Command\CoreAdmin;

use Solrphp\SolariumBundle\Tests\Integration\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Solr Core Status Command Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreStatusCommandTest extends KernelTestCase
{
    /**
     * boot fresh kernel for each test.
     */
    protected function setUp(): void
    {
        self::bootKernel(['environment' => TestKernel::ENVIRONMENT_STANDALONE]);
    }

    /**
     * get core status.
     */
    public function testExecuteStatus(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('solr:core:status');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--core' => 'demo',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());

        $display = $commandTester->getDisplay();

        self::assertStringContainsString(' status ', $display);
        self::assertStringContainsString(' index ', $display);
        self::assertStringContainsString(' user data ', $display);
    }

    /**
     * get core status omit index.
     */
    public function testExecuteStatusNoIndex(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('solr:core:status');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--core' => 'demo',
            '--omit-index-info' => null,
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());

        $display = $commandTester->getDisplay();

        self::assertStringContainsString(' status ', $display);
        self::assertStringNotContainsString(' index ', $display);
        self::assertStringNotContainsString(' user data ', $display);
    }

    /**
     * @return string
     */
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
