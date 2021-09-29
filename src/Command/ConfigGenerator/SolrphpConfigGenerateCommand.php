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
            ConfigGenerator::TYPE_FIELD => !$input->getOption('exclude-fields'),
            ConfigGenerator::TYPE_COPY_FIELD => !$input->getOption('exclude-copy-fields'),
            ConfigGenerator::TYPE_DYNAMIC_FIELD => !$input->getOption('exclude-dynamic-fields'),
            ConfigGenerator::TYPE_FIELD_TYPE => !$input->getOption('exclude-field-types'),
        ]));
    }
}
