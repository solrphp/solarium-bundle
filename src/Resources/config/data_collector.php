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

use Solrphp\SolariumBundle\DataCollector\SolrCallRegistry;
use Solrphp\SolariumBundle\DataCollector\SolrCollector;
use Solrphp\SolariumBundle\DataCollector\SolrRequestSubscriber;

/*
 * configure solrphp serializer.
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(SolrCallRegistry::class)
        ->alias('solrphp.collector.registry', SolrCallRegistry::class)

        ->set(SolrRequestSubscriber::class)
        ->args([
            service('solrphp.collector.registry'),
        ])
        ->tag('kernel.event_subscriber')
        ->alias('solrphp.collector.subscriber', SolrRequestSubscriber::class)

        ->set(SolrCollector::class)
        ->args([
            service('solrphp.collector.registry'),
        ])
        ->tag('data_collector', [
            'id' => SolrCollector::class,
            'template' => '@SolrphpSolarium/data_collector/solr.html.twig',
        ])
    ;
};
