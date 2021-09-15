<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$autoloader = require __DIR__.'/../vendor/autoload.php';

use Doctrine\Common\Annotations\AnnotationRegistry;
use Solrphp\SolariumBundle\DependencyInjection\SolrphpSolariumExtension;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

$container = new ContainerBuilder(new ParameterBag([
    'kernel.debug' => true,
    'kernel.container_class' => 'foo',
    'kernel.project_dir' => __DIR__.'/../src',
    'kernel.bundles_metadata' => [],
    'kernel.build_dir' => __DIR__,
    'kernel.cache_dir' => __DIR__.'/../var/cache/test',
]));

$container->registerExtension(new FrameworkExtension());
$container->registerExtension(new SolrphpSolariumExtension());

$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Stub/config'));
$loader->load('solrphp_solarium.yaml');

$container->loadFromExtension('framework', []);
$container->loadFromExtension('solrphp_solarium', $container->getExtensionConfig('solrphp_solarium')[0]);

$container->getCompilerPassConfig()->setOptimizationPasses([]);
$container->getCompilerPassConfig()->setRemovingPasses([]);
$container->compile();

$dumper = new XmlDumper($container);

file_put_contents(__DIR__.'/../var/cache/test/Solrphp_TestContainer.xml', $dumper->dump());
