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
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solrphp\SolariumBundle\Command\Config\SolrConfigUpdateCommand;
use Solrphp\SolariumBundle\Command\ConfigGenerator\SolrphpConfigGenerateCommand;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\DataCollector\SolrCallRegistry;
use Solrphp\SolariumBundle\DataCollector\SolrCollector;
use Solrphp\SolariumBundle\DataCollector\SolrRequestSubscriber;
use Solrphp\SolariumBundle\DependencyInjection\SolrphpSolariumExtension;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Solr Api Extension Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
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
        $schemaConfig = [
            'clients' => [
                'default' => [],
            ],
            'managed_schemas' => [
                [
                    'cores' => ['demo'],
                    'unique_key' => 'foo',
                ],
            ],
        ];

        $configConfig = [
            'clients' => [
                'default' => [],
            ],
            'solr_configs' => [
                [
                  'cores' => ['demo'],
                ],
            ],
        ];
        $container = $this->createContainer();
        $this->extension->load([$this->getBaseConfig()], $container);

        // no schema nor config, no need for the configuration store
        self::assertFalse($container->hasDefinition(SolrConfigurationStore::class));

        $this->extension->load([$schemaConfig], $container);

        self::assertTrue($container->hasDefinition(SolrConfigurationStore::class));

        $definition = $container->getDefinition(SolrConfigurationStore::class);

        self::assertTrue($container->hasDefinition(SolrSerializer::class));
        self::assertTrue($container->hasDefinition(ConfigGenerator::class));
        self::assertTrue($container->hasDefinition(SolrphpConfigGenerateCommand::class));

        self::assertCount(3, $definition->getArguments());

        // test with new container config config only
        $container = $this->createContainer();
        $this->extension->load([$configConfig], $container);
        self::assertTrue($container->hasDefinition(SolrConfigurationStore::class));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLoadCoreManager(): void
    {
        $container = $this->createContainer();
        $this->extension->load([$this->getBaseConfig()], $container);

        // no client, no core manager
        self::assertFalse($container->hasDefinition('solrphp.manager.core_admin'));

        $this->extension->load($this->getClientConfig(), $container);
        self::assertTrue($container->hasDefinition('solrphp.manager.core_admin'));

        $definition = $container->getDefinition('solrphp.manager.core_admin');

        self::assertCount(2, $definition->getArguments());

        self::assertTrue($container->hasAlias(CoreManager::class));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLoadSchemaManager(): void
    {
        $config = [
            'clients' => [
                'default' => [],
            ],
            'managed_schemas' => [
                [
                    'cores' => ['demo'],
                    'unique_key' => 'foo',
                ],
            ],
        ];

        $container = $this->createContainer();
        $this->extension->load([$this->getBaseConfig()], $container);

        // no client, no schema manager
        self::assertFalse($container->hasDefinition('solrphp.manager.schema'));

        $this->extension->load([$config], $container);
        self::assertTrue($container->hasDefinition('solrphp.manager.schema'));

        $definition = $container->getDefinition('solrphp.manager.schema');

        self::assertCount(3, $definition->getArguments());

        self::assertTrue($container->hasAlias(SchemaManager::class));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLoadDataCollector(): void
    {
        $config = [
            'clients' => [
                'default' => [],
            ],
        ];

        $container = $this->createContainer();
        $this->extension->load([$config], $container);

        self::assertFalse($container->hasDefinition(SolrCollector::class));
        self::assertFalse($container->hasDefinition(SolrCallRegistry::class));
        self::assertFalse($container->hasDefinition(SolrRequestSubscriber::class));

        $container = $this->createContainer(true);
        $this->extension->load([$config], $container);

        self::assertTrue($container->hasDefinition(SolrCollector::class));
        self::assertTrue($container->hasDefinition(SolrCallRegistry::class));
        self::assertTrue($container->hasDefinition(SolrRequestSubscriber::class));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLoadConfigManager(): void
    {
        $configConfig = [
            'clients' => [
                'default' => [],
            ],
            'solr_configs' => [
                [
                    'cores' => ['demo'],
                ],
            ],
        ];

        $container = $this->createContainer();
        $this->extension->load([$this->getBaseConfig()], $container);

        // no client, no schema manager
        self::assertFalse($container->hasDefinition('solrphp.manager.config'));
        self::assertFalse($container->hasDefinition('solrphp.command.config_update'));

        $this->extension->load([$configConfig], $container);
        self::assertTrue($container->hasDefinition('solrphp.manager.config'));

        $definition = $container->getDefinition('solrphp.manager.config');

        self::assertCount(3, $definition->getArguments());
        self::assertTrue($container->hasAlias(ConfigManager::class));

        // config update command should be registered as well
        self::assertTrue($container->hasDefinition('solrphp.command.config_update'));

        $definition = $container->getDefinition('solrphp.command.config_update');

        self::assertTrue($definition->hasTag('console.command'));
        self::assertCount(2, $definition->getArguments());
        self::assertTrue($container->hasAlias(SolrConfigUpdateCommand::class));
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
                    'username' => 'bar',
                    'password' => 'foo',
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
        self::assertSame(['core' => 'bar', 'username' => 'bar', 'password' => 'foo', 'key' => 'second'], $second->getArgument(0));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     */
    public function testLoadClients(): void
    {
        $container = $this->createContainer();
        $this->extension->load($this->getClientConfig(), $container);

        // client definitions
        self::assertTrue($container->hasDefinition('solarium.client.default'));
        self::assertTrue($container->hasDefinition('solarium.client.second'));
        self::assertTrue($container->hasDefinition('solarium.client.third'));

        // core manager should be available at this point as well
        self::assertTrue($container->hasDefinition('solrphp.manager.core_admin'));

        //client configurations
        $default = $container->getDefinition('solarium.client.default');

        self::assertSame(Client::class, $default->getClass());
        self::assertSame('solarium.adapter.default', (string) $default->getArgument(0));
        self::assertSame('event_dispatcher', (string) $default->getArgument(1));

        $second = $container->getDefinition('solarium.client.second');

        self::assertSame('\Foo\Bar', $second->getClass());
        self::assertSame('adapter.service', (string) $second->getArgument(0));
        self::assertSame('dispatcher.service', (string) $second->getArgument(1));

        self::assertTrue($second->hasMethodCall('setDefaultEndpoint'));
        self::assertContains('second', $second->getMethodCalls()[0][1]);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     */
    public function testEndpointAssignment(): void
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
                'default' => [],
                'second' => [
                    'endpoints' => [
                        'second',
                    ],
                ],
                'third' => [
                    'endpoints' => [
                        'third',
                    ],
                ],
                'fourth' => [
                    'endpoints' => [
                        'third',
                        'second',
                    ],
                ],
            ],
        ];

        $container = $this->createContainer();
        $this->extension->load([$config], $container);

        self::assertTrue($container->hasDefinition('solarium.client.default'));
        self::assertTrue($container->hasDefinition('solarium.client.second'));
        self::assertTrue($container->hasDefinition('solarium.client.third'));
        self::assertTrue($container->hasDefinition('solarium.client.fourth'));

        $default = $container->getDefinition('solarium.client.default');

        // assign all endpoints if none defined
        self::assertArrayHasKey('default', $default->getArgument(2)['endpoint']);
        self::assertArrayHasKey('second', $default->getArgument(2)['endpoint']);

        $second = $container->getDefinition('solarium.client.second');

        // assign defined endpoints
        self::assertArrayHasKey('second', $second->getArgument(2)['endpoint']);
        self::assertArrayNotHasKey('default', $second->getArgument(2)['endpoint']);

        $third = $container->getDefinition('solarium.client.third');

        // non-existing endpoint configured, all assigned
        self::assertArrayHasKey('default', $third->getArgument(2)['endpoint']);
        self::assertArrayHasKey('second', $third->getArgument(2)['endpoint']);

        $fourth = $container->getDefinition('solarium.client.fourth');

        // only existing endpoints are assigned
        self::assertArrayHasKey('second', $fourth->getArgument(2)['endpoint']);
        self::assertArrayNotHasKey('default', $fourth->getArgument(2)['endpoint']);
        self::assertArrayNotHasKey('third', $fourth->getArgument(2)['endpoint']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testDefaultAliasAssignment(): void
    {
        $config = [
            'clients' => [
                'default' => [],
                'second' => [],
            ],
        ];

        $container = $this->createContainer();
        $this->extension->load([$config], $container);

        self::assertTrue($container->hasDefinition('solarium.client.default'));
        self::assertTrue($container->hasDefinition('solarium.client.second'));
        self::assertTrue($container->hasAlias('solarium.client'));

        $alias = $container->getAlias('solarium.client');

        // none configured, defaults to 'default'
        self::assertSame('solarium.client.default', (string) $alias);
        self::assertTrue($alias->isPublic());

        // client class is assigned as alias as well
        self::assertTrue($container->hasAlias(Client::class));

        $alias = $container->getAlias(Client::class);

        self::assertSame('solarium.client.default', (string) $alias);
        self::assertTrue($alias->isPublic());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConfiguredAliasAssignment(): void
    {
        $config = [
            'default_client' => 'second',
            'clients' => [
                'default' => [],
                'second' => [
                    'client_class' => 'Foo\Bar',
                ],
            ],
        ];

        $container = $this->createContainer();
        $this->extension->load([$config], $container);

        self::assertTrue($container->hasDefinition('solarium.client.default'));
        self::assertTrue($container->hasDefinition('solarium.client.second'));
        self::assertTrue($container->hasAlias('solarium.client'));

        $alias = $container->getAlias('solarium.client');

        // none configured, defaults to 'default'
        self::assertSame('solarium.client.second', (string) $alias);
        self::assertTrue($alias->isPublic());

        // client class is assigned as alias as well
        self::assertTrue($container->hasAlias('Foo\Bar'));

        $alias = $container->getAlias('Foo\Bar');
        self::assertSame('solarium.client.second', (string) $alias);
        self::assertTrue($alias->isPublic());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAdapterAssignment(): void
    {
        $config = [
            'clients' => [
                'default' => [
                    'adapter_service' => 'my.adapter.service',
                ],
                'second' => [],
                'third' => [
                    'adapter_class' => 'Foo\Bar',
                ],
            ],
        ];

        $container = $this->createContainer();
        $this->extension->load([$config], $container);

        self::assertTrue($container->hasDefinition('solarium.client.default'));
        self::assertTrue($container->hasDefinition('solarium.client.second'));
        self::assertTrue($container->hasDefinition('solarium.client.third'));

        // adapter service defined, no custom definition
        self::assertFalse($container->hasDefinition('my.adapter.service'));

        // nothing configured, default adapter class
        self::assertTrue($container->hasDefinition('solarium.adapter.second'));

        $adapter = $container->getDefinition('solarium.adapter.second');

        self::assertSame(Curl::class, $adapter->getClass());

        // custom adapter class
        self::assertTrue($container->hasDefinition('solarium.adapter.third'));

        $adapter = $container->getDefinition('solarium.adapter.third');

        self::assertSame('Foo\Bar', $adapter->getClass());
    }

    /**
     * @param bool $debug
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function createContainer(bool $debug = false): ContainerBuilder
    {
        return new ContainerBuilder(
            new ParameterBag(['kernel.debug' => $debug])
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function getBaseConfig(): array
    {
        return [
            'clients' => [
                'deafult' => [
                    'endpoints' => ['demo'],
                ],
            ],
            'managed_schemas' => [],
            'solr_configs' => [],
        ];
    }

    /**
     * @return array<int, array<string, array<string, array<string, array<int, string>|string>>|string>>
     */
    private function getClientConfig(): array
    {
        return
        [
            [
                'endpoints' => [
                    'default' => [
                        'core' => 'foo',
                    ],
                    'second' => [
                        'core' => 'bar',
                    ],
                ],
                'default_client' => 'third',
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
                    'third' => [],
                ],
            ],
        ];
    }
}
