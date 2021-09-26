<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Solrphp\SolariumBundle\Command\Config\SolrConfigUpdateCommand;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigGenerator;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;

/*
 * configure solr config api services and commands
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->instanceof(ConfigNodeHandlerInterface::class)
        ->tag('solrphp.config_node_processor')

        ->set('solrphp.manager.config', ConfigManager::class)
            ->args([
                service('solarium.client'),
                service('solrphp.manager.core_admin'),
                service('solrphp.serializer'),
            ])
        ->alias(ConfigManager::class, 'solrphp.manager.config')

        ->set('solrphp.generator.config', ConfigGenerator::class)
            ->args([
                service('solrphp.serializer'),
            ])
        ->alias(ConfigGenerator::class, 'solrphp.generator.config')

        ->set('solrphp.processor.config', ConfigProcessor::class)
            ->args([
                tagged_iterator('solrphp.config_node_processor'),
                service('solrphp.manager.config'),
            ])
        ->alias(ConfigProcessor::class, 'solrphp.processor.config')

        ->set('solrphp.command.config_update', SolrConfigUpdateCommand::class)
            ->args([
                service('solrphp.processor.config'),
                service(SolrConfigurationStore::class),
            ])
        ->tag('console.command')
        ->alias(SolrConfigUpdateCommand::class, 'solrphp.command.config_update')

        // load config processors for this api.
        ->load('Solrphp\\SolariumBundle\\SolrApi\\Config\\Manager\\Handler\\', '../../SolrApi/Config/Manager/Handler')
    ;
};
