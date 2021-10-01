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

use Solrphp\SolariumBundle\Command\Param\SolrParamUpdateCommand;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\SolrApi\Param\Generator\ParamsGenerator;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamManager;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;

/*
 * configure solr config api services and commands
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->instanceof(ConfigNodeHandlerInterface::class)
        ->tag('solrphp.config_node_processor')

        ->set(ParamManager::class)
        ->args([
            service('solarium.client'),
            service('solrphp.manager.core_admin'),
            service('solrphp.serializer'),
        ])
        ->alias('solrphp.manager.param', ParamManager::class)

        ->set(ParamsGenerator::class)
        ->args([
            service('solrphp.serializer'),
        ])
        ->alias('solrphp.generator.param', ParamsGenerator::class)

        ->set(ParamProcessor::class)
        ->args([
            tagged_iterator('solrphp.config_node_processor'),
            service('solrphp.manager.param'),
        ])
        ->alias('solrphp.processor.param', ParamProcessor::class)

        ->set(SolrParamUpdateCommand::class)
        ->args([
            service('solrphp.processor.param'),
            service(SolrConfigurationStore::class),
        ])
        ->tag('console.command')
        ->alias('solrphp.command.schema_update', SolrParamUpdateCommand::class)

        // load config processors for this api.
        ->load('Solrphp\\SolariumBundle\\SolrApi\\Param\\Manager\\Handler\\', '../../SolrApi/Param/Manager/Handler')
    ;
};
