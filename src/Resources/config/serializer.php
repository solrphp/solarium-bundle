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

use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;

/*
 * configure solr schema api services and commands
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('solrphp.serializer', SolrSerializer::class)
        ->alias(SolrSerializer::class, 'solrphp.serializer')
    ;
};
