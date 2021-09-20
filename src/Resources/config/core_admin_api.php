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

use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;

/*
 * configure core admin manager
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('solrphp.manager.core_admin', CoreManager::class)
        ->args([
            service('solarium.client'),
            service('serializer'),
        ])
        ->alias(CoreManager::class, 'solrphp.manager.core_admin')
    ;
};
