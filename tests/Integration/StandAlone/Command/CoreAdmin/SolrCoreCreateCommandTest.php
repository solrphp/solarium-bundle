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
 * Solr Core Create Command Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreCreateCommandTest extends KernelTestCase
{
    private static string $name;

    /**
     * boot new kernel for each test.
     */
    protected function setUp(): void
    {
        self::bootKernel(['environment' => TestKernel::ENVIRONMENT_STANDALONE]);
        self::$name = bin2hex(random_bytes(4));
    }

    /**
     * delete any created resources.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        fopen(sprintf('http://localhost:8983/solr/admin/cores?action=UNLOAD&core=%s&deleteIndex=true&deleteDataDir=true&deleteInstanceDir=true', self::$name), 'rb');
    }

    /**
     * get core status.
     */
    public function testExecuteCreateFailureNoConfig(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('solr:core:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'core' => self::$name,
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertStringContainsString('Caused by: Can\'t find resource \'solrconfig.xml\' in classpath', $commandTester->getDisplay());
    }

    /**
     * get core status.
     */
    public function testExecuteCreateFailureSchema(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('solr:core:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'core' => self::$name,
            '--config' => '../demo/conf/solrconfig.xml',
        ]);

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertStringContainsString('Caused by: Can\'t find resource \'schema.xml\' in classpath ', $commandTester->getDisplay());
    }

    /**
     * get core status.
     */
    public function testExecuteCreate(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('solr:core:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'core' => self::$name,
            '--config-set' => '/opt/solr/server/solr/configsets/_default',
        ]);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        self::assertStringContainsString('successfully created core ', $commandTester->getDisplay());
    }

    /**
     * @return string
     */
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
