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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solr Core Unload Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreSwapCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:swap';

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
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('rename a solr core')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'name of one of the cores to be swapped'),
                new InputArgument('other', InputArgument::REQUIRED, 'name of one of the cores to be swapped.'),
                new InputOption('async', null, InputOption::VALUE_REQUIRED, 'request id to track this action which will be processed asynchronously'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->swap($this->getOptions($input));

        if (0 !== $response->getResponseHeader()->getStatus()) {
            $error = null !== $response->getError() ? $response->getError()->getMessage() : '[unable to get error message]';

            $output->writeln(sprintf('<error>error swapping cores %s & %s: %s (%d)</error>', $input->getArgument('core'), $input->getArgument('other'), $error, $response->getResponseHeader()->getStatus()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully swapped cores %s & %s</info>', $input->getArgument('core'), $input->getArgument('other')));

        return Command::SUCCESS;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string, bool|string>
     */
    private function getOptions(InputInterface $input): array
    {
        $options = [];

        if (null !== ($core = $input->getArgument('core'))) {
            $options['core'] = $core;
        }

        if (null !== ($other = $input->getArgument('other'))) {
            $options['other'] = $other;
        }

        if (null !== ($value = $input->getOption('async'))) {
            $options['async'] = $value;
        }

        return $options;
    }
}
