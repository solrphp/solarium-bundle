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

use Solrphp\SolariumBundle\Command\Schema\SolrSchemaUpdateCommand;
use Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface;
use Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaGenerator;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;

/*
 * configure solr schema api services and commands
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->instanceof(ConfigNodeHandlerInterface::class)
        ->tag('solrphp.config_node_processor')

        ->set('solrphp.manager.schema', SchemaManager::class)
            ->args([
                service('solarium.client'),
                service('solrphp.manager.core_admin'),
                service('solrphp.serializer'),
            ])
        ->alias(SchemaManager::class, 'solrphp.manager.schema')

        ->set('solrphp.generator.schema', SchemaGenerator::class)
            ->args([
                service('solrphp.serializer'),
            ])
        ->alias(SchemaGenerator::class, 'solrphp.generator.schema')

        ->set('solrphp.processor.schema', SchemaProcessor::class)
            ->args([
                tagged_iterator('solrphp.config_node_processor'),
                service('solrphp.manager.schema'),
            ])
        ->alias(SchemaProcessor::class, 'solrphp.processor.schema')

        ->set('solrphp.command.schema_update', SolrSchemaUpdateCommand::class)
            ->args([
                service('solrphp.processor.schema'),
                service(SolrConfigurationStore::class),
            ])
        ->tag('console.command')
        ->alias(SolrSchemaUpdateCommand::class, 'solrphp.command.schema_update')

        // load config processors for this api.
        ->load('Solrphp\\SolariumBundle\\SolrApi\\Schema\\Manager\\Handler\\', '../../SolrApi/Schema/Manager/Handler')
        ->exclude(['*Trait'])
    ;
};
