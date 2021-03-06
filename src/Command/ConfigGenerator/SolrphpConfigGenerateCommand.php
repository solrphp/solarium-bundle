<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Command\ConfigGenerator;

use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ConfigConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ParamConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solrphp ConfigGenerate Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrphpConfigGenerateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:config:generate';

    /**
     * @var \Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator
     */
    private ConfigGenerator $generator;

    /**
     * @param \Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator $generator
     */
    public function __construct(ConfigGenerator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('generates solrphp config file from managed schema')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'solr core for which to generate the config'),
                new InputArgument('filetype', InputArgument::REQUIRED, 'format of generated config file (yaml||php)'),
                new InputOption('exclude-fields', null, InputOption::VALUE_NONE, 'do not dump field definitions'),
                new InputOption('exclude-copy-fields', null, InputOption::VALUE_NONE, 'do not dump copy field definitions'),
                new InputOption('exclude-dynamic-fields', null, InputOption::VALUE_NONE, 'do not dump dynamic field definitions'),
                new InputOption('exclude-field-types', null, InputOption::VALUE_NONE, 'do not dump field type definitions'),
                new InputOption('exclude-update-handler', null, InputOption::VALUE_NONE, 'do not dump update handler definition'),
                new InputOption('exclude-query', null, InputOption::VALUE_NONE, 'do not dump query definition'),
                new InputOption('exclude-request-dispatcher', null, InputOption::VALUE_NONE, 'do not dump request dispatcher definitions'),
                new InputOption('exclude-request-handlers', null, InputOption::VALUE_NONE, 'do not dump request handler definitions'),
                new InputOption('exclude-search-components', null, InputOption::VALUE_NONE, 'do not dump search component definitions'),
                new InputOption('exclude-parameters', null, InputOption::VALUE_NONE, 'do not dump parameter definitions'),
            ])
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->generator
                ->withCore($input->getArgument('core'))
                ->withExtension($input->getArgument('filetype'))
                ->withTypes($this->getTypes($input))
                ->generate();
        } catch (GeneratorException $e) {
            $output->writeln(sprintf('<error>error generating config for %s core</error>', $input->getArgument('core')));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully generated config for %s core</info>', $input->getArgument('core')));

        return Command::SUCCESS;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string>
     */
    private function getTypes(InputInterface $input): array
    {
        return array_keys(array_filter([
            SchemaConfigurationGenerator::TYPE_FIELD => !$input->getOption('exclude-fields'),
            SchemaConfigurationGenerator::TYPE_COPY_FIELD => !$input->getOption('exclude-copy-fields'),
            SchemaConfigurationGenerator::TYPE_DYNAMIC_FIELD => !$input->getOption('exclude-dynamic-fields'),
            SchemaConfigurationGenerator::TYPE_FIELD_TYPE => !$input->getOption('exclude-field-types'),
            ConfigConfigurationGenerator::TYPE_UPDATE_HANDLER => !$input->getOption('exclude-update-handler'),
            ConfigConfigurationGenerator::TYPE_QUERY => !$input->getOption('exclude-query'),
            ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER => !$input->getOption('exclude-request-dispatcher'),
            ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER => !$input->getOption('exclude-request-handlers'),
            ConfigConfigurationGenerator::TYPE_SEARCH_COMPONENT => !$input->getOption('exclude-search-components'),
            ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP => !$input->getOption('exclude-parameters'),
        ]));
    }
}
