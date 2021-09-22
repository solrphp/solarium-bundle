<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Command\CoreAdmin;

use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solr Core Unload Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreSplitCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:split';

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager
     */
    private CoreManager $coreManager;

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager $coreManager
     */
    public function __construct(CoreManager $coreManager)
    {
        parent::__construct();

        $this->coreManager = $coreManager;
    }

    /**
     * additional option validation.
     *
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     *
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption('path') && $input->getOption('target-core')) {
            throw new RuntimeException('either "path" or "target-core" can be defined, not both');
        }

        if ($input->getOption('ranges') && $input->getOption('split-key')) {
            throw new RuntimeException('either "ranges" or "split-key" can be defined, not both');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('splits an index into two or more indexes')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'name of one of the cores to be swapped'),
                new InputOption('path', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'multi-valued, the directory path in which a piece of the index will be written'),
                new InputOption('target-core', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'multi-valued, the target solr core to which a piece of the index will be merged'),
                new InputOption('ranges', null, InputOption::VALUE_REQUIRED, 'comma-separated list of hash ranges in hexadecimal format'),
                new InputOption('split-key', null, InputOption::VALUE_REQUIRED, 'key to be used for splitting the index'),
                new InputOption('async', null, InputOption::VALUE_REQUIRED, 'request id to track this action which will be processed asynchronously'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->split($this->getOptions($input));

        if (0 !== $response->getHeader()->getStatusCode()) {
            $error = null !== $response->getError() ? $response->getError()->getMessage() : '[unable to get error message]';

            $output->writeln(sprintf('<error>error splitting core %s: %s (%d)</error>', $input->getArgument('core'), $error, $response->getHeader()->getStatusCode()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully split core %s</info>', $input->getArgument('core')));

        return Command::SUCCESS;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string, string|array<string>>
     */
    private function getOptions(InputInterface $input): array
    {
        $options = [];

        if (null !== ($core = $input->getArgument('core'))) {
            $options['core'] = $core;
        }

        if (null !== ($path = $input->getOption('path'))) {
            $options['path'] = $path;
        }

        if (null !== ($targetCore = $input->getOption('target-core'))) {
            $options['targetCore'] = $targetCore;
        }

        if (null !== ($ranges = $input->getOption('ranges'))) {
            $options['ranges'] = $ranges;
        }

        if (null !== ($splitKey = $input->getOption('split-key'))) {
            $options['split.key'] = $splitKey;
        }

        if (null !== ($value = $input->getOption('async'))) {
            $options['async'] = $value;
        }

        return $options;
    }
}
