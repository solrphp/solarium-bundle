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

use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreCreateCommand;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreMergeIndexesCommand;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreReloadCommand;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreRenameCommand;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreSplitCommand;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreStatusCommand;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreSwapCommand;
use Solrphp\SolariumBundle\Command\CoreAdmin\SolrCoreUnloadCommand;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;

/*
 * configure core admin manager and associated commands
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('solrphp.manager.core_admin', CoreManager::class)
            ->args([
                service('solarium.client'),
                service('solrphp.serializer'),
            ])
        ->alias(CoreManager::class, 'solrphp.manager.core_admin')

        ->set('solrphp.command.core_create', SolrCoreCreateCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreCreateCommand::class, 'solrphp.command.core_create')

        ->set('solrphp.command.core_merge_indexes', SolrCoreMergeIndexesCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreMergeIndexesCommand::class, 'solrphp.command.core_merge_indexes')

        ->set('solrphp.command.core_reload', SolrCoreReloadCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreReloadCommand::class, 'solrphp.command.core_reload')

        ->set('solrphp.command.core_rename', SolrCoreRenameCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreRenameCommand::class, 'solrphp.command.core_rename')

        ->set('solrphp.command.core_split', SolrCoreSplitCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreSplitCommand::class, 'solrphp.command.core_split')

        ->set('solrphp.command.core_status', SolrCoreStatusCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreStatusCommand::class, 'solrphp.command.core_status')

        ->set('solrphp.command.core_swap', SolrCoreSwapCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreSwapCommand::class, 'solrphp.command.core_swap')

        ->set('solrphp.command.core_unload', SolrCoreUnloadCommand::class)
            ->args([
                service('solrphp.manager.core_admin'),
            ])
        ->tag('console.command')
        ->alias(SolrCoreUnloadCommand::class, 'solrphp.command.core_reload')
    ;
};
