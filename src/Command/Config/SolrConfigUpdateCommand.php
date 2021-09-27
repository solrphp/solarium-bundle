<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Command\Config;

use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solr Config Update Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrConfigUpdateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:config:update';

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore
     */
    private SolrConfigurationStore $configurationStore;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigProcessor
     */
    private ConfigProcessor $processor;

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigProcessor $processor
     * @param \Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore         $configurationStore
     */
    public function __construct(ConfigProcessor $processor, SolrConfigurationStore $configurationStore)
    {
        parent::__construct();

        $this->configurationStore = $configurationStore;
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('modifies solr config overlay for given core')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'solr core for which to update the config'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $core = $input->getArgument('core');

        if (null === $config = $this->configurationStore->getConfigForCore($core)) {
            $output->writeln(sprintf('<error>No config found for %s core</error>', $core));

            return Command::INVALID;
        }

        try {
            $this->processor
                ->withCore($core)
                ->withConfig($config)
                ->process()
            ;
        } catch (ProcessorException $e) {
            /* @infection-ignore-all tested @ \Solrphp\SolariumBundle\Tests\Unit\Command\SolrConfigUpdateCommandTest::testExecuteFailure */
            $output->writeln(sprintf('<error>Unable to process config for %s core: %s</error>', $core, $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>Successfully updated config for %s core</info>', $core));

        return Command::SUCCESS;
    }
}
