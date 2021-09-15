<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solrphp\SolariumBundle\DependencyInjection\SolrphpSolariumExtension;
use Solrphp\SolariumBundle\SolrApi\Config\SolrConfigurationStore;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Solr Api Extension Test.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class SolrApiExtensionTest extends TestCase
{
    /**
     * @var \Solrphp\SolariumBundle\DependencyInjection\SolrphpSolariumExtension
     */
    private SolrphpSolariumExtension $extension;

    /**
     * set up.
     */
    protected function setUp(): void
    {
        $this->extension = new SolrphpSolariumExtension();
    }

    /**
     * test load.
     */
    public function testLoadConfigurationStore(): void
    {
        $container = $this->createContainer();
        $this->extension->load([$this->getBaseConfig()], $container);

        self::assertTrue($container->hasDefinition(SolrConfigurationStore::class));

        $definition = $container->getDefinition(SolrConfigurationStore::class);

        self::assertCount(2, $definition->getArguments());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     */
    public function testLoadEndpoints(): void
    {
        $config = [
            'endpoints' => [
                'default' => [
                    'core' => 'foo',
                ],
                'second' => [
                    'core' => 'bar',
                ],
            ],
        ];

        $container = $this->createContainer();

        $this->extension->load([$config], $container);

        self::assertTrue($container->hasDefinition('solarium.client.endpoint.default'));
        self::assertTrue($container->hasDefinition('solarium.client.endpoint.second'));

        $default = $container->getDefinition('solarium.client.endpoint.default');
        $second = $container->getDefinition('solarium.client.endpoint.second');

        self::assertSame(['core' => 'foo', 'key' => 'default'], $default->getArgument(0));
        self::assertSame(['core' => 'bar', 'key' => 'second'], $second->getArgument(0));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     */
    public function testLoadClients(): void
    {
        $config = [
            'endpoints' => [
                'default' => [
                    'core' => 'foo',
                ],
                'second' => [
                    'core' => 'bar',
                ],
            ],
            'clients' => [
                'default' => [
                    'endpoints' => [
                        'default',
                    ],
                ],
                'second' => [
                    'endpoints' => [
                        'default',
                        'second',
                    ],
                    'default_endpoint' => 'second',
                    'client_class' => '\Foo\Bar',
                    'adapter_service' => 'adapter.service',
                    'dispatcher_service' => 'dispatcher.service',
                ],
            ],
        ];

        $container = $this->createContainer();

        $this->extension->load([$config], $container);

        self::assertTrue($container->hasDefinition('solarium.client.default'));
        self::assertTrue($container->hasDefinition('solarium.client.second'));

        $default = $container->getDefinition('solarium.client.default');

        self::assertSame(Client::class, $default->getClass());
        self::assertSame('solarium.adapter.default', (string) $default->getArgument(0));
        self::assertSame('event_dispatcher', (string) $default->getArgument(1));
        self::assertArrayHasKey('default', $default->getArgument(2)['endpoints']);

        $second = $container->getDefinition('solarium.client.second');

        self::assertSame('\Foo\Bar', $second->getClass());
        self::assertSame('adapter.service', (string) $second->getArgument(0));
        self::assertSame('dispatcher.service', (string) $second->getArgument(1));
        self::assertArrayHasKey('default', $second->getArgument(2)['endpoints']);
        self::assertArrayHasKey('second', $second->getArgument(2)['endpoints']);

        self::assertTrue($second->hasMethodCall('setDefaultEndpoint'));
        self::assertContains('second', $second->getMethodCalls()[0][1]);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function createContainer(): ContainerBuilder
    {
        return new ContainerBuilder(
            new ParameterBag(['kernel.debug' => false])
        );
    }

    /**
     * @return array[]
     */
    private function getBaseConfig(): array
    {
        return [
            'managed_schemas' => [],
            'solr_configs' => [],
        ];
    }
}
