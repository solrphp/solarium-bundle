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

use Solrphp\SolariumBundle\Command\ConfigGenerator\SolrphpConfigGenerateCommand;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface;

/*
 * configure solrphp serializer.
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->instanceof(ConfigGeneratorHandlerInterface::class)
        ->tag('solrphp.config_generator_handler')

        ->instanceof(DumperInterface::class)
        ->tag('solrphp.config_dumper')

        ->set(SolrSerializer::class)
        ->alias('solrphp.serializer', SolrSerializer::class)

        ->set(ConfigGenerator::class)
            ->args([
                tagged_iterator('solrphp.config_generator_handler'),
                tagged_iterator('solrphp.config_dumper'),
                param('kernel.project_dir'),
                service('solarium.client.default'),
            ])
        ->alias('solrphp.config_generator', ConfigGenerator::class)

        ->set(SolrphpConfigGenerateCommand::class)
            ->args([
                service('solrphp.config_generator'),
            ])
        ->tag('console.command')
        ->alias('solrphp.command.config_generate', SolrphpConfigGenerateCommand::class)

        ->load('Solrphp\\SolariumBundle\\ConfigGenerator\\', '../../ConfigGenerator/{Handler,Dumper,Handler/Visitor}')
    ;
};
