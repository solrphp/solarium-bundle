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

use Solrphp\SolariumBundle\Command\SolrConfigUpdateCommand;
use Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;

/*
 * configure solr config api services and commands
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(ConfigNodeProcessorInterface::class)
            ->tag('solrphp.config_node_processor')

        ->set('solrphp.manager.config', ConfigManager::class)
            ->args([
                service('solarium.client'),
                service('solrphp.manager.core_admin'),
                service('serializer'),
            ])
        ->alias(ConfigManager::class, 'solrphp.manager.config')

        ->set('solrphp.processor.config', ConfigProcessor::class)
            ->args([
                tagged_iterator('solrphp.config_node_processor'),
                service('solrphp.manager.config'),
            ])
        ->alias(ConfigProcessor::class, 'solrphp.processor.config')

        ->set('solrphp.command.config_update', SolrConfigUpdateCommand::class)
            ->args([
                service('solrphp.manager.config'),
                service(SolrConfigurationStore::class),
            ])
        ->tag('console.command')
        ->alias(SolrConfigUpdateCommand::class, 'solrphp.command.config_update')
    ;
};
