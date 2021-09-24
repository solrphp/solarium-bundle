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

use Solrphp\SolariumBundle\Common\Util\ErrorUtil;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solr Core Reload Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreCreateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:create';

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
            ->setDescription('creates a solr core')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'solr core to create'),
                new InputOption('instance-dir', null, InputOption::VALUE_REQUIRED, 'directory where files for this core should be stored. default is the value specified for the name parameter if not supplied'),
                new InputOption('config', null, InputOption::VALUE_REQUIRED, 'name of the config file (i.e. solrconfig.xml) relative to instance-dir'),
                new InputOption('schema', null, InputOption::VALUE_REQUIRED, 'name of the schema file to use for the core.'),
                new InputOption('data-dir', null, InputOption::VALUE_REQUIRED, 'name of the data directory relative to instance-dir.'),
                new InputOption('config-set', null, InputOption::VALUE_REQUIRED, 'name of the configset to use for this core.'),
                new InputOption('collection', null, InputOption::VALUE_REQUIRED, 'name of the collection to which this core belongs. default is the name of the core'),
                new InputOption('shard', null, InputOption::VALUE_REQUIRED, 'shard id this core represents'),
                new InputOption('async', null, InputOption::VALUE_REQUIRED, 'request id to track this action which will be processed asynchronously'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->create($this->getOptions($input));

        if (0 !== $response->getResponseHeader()->getStatus()) {
            $output->writeln(sprintf('<error>error creating core %s: %s</error>', $input->getArgument('core'), ErrorUtil::fromResponse($response, $output->getVerbosity())));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully created core %s</info>', $input->getArgument('core')));

        return Command::SUCCESS;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string, string>
     */
    private function getOptions(InputInterface $input): array
    {
        $options = [];

        if (null !== ($core = $input->getArgument('core'))) {
            $options['name'] = $core;
        }

        if (null !== ($instanceDir = $input->getOption('instance-dir'))) {
            $options['instanceDir'] = $instanceDir;
        }

        if (null !== ($config = $input->getOption('config'))) {
            $options['config'] = $config;
        }

        if (null !== ($schema = $input->getOption('schema'))) {
            $options['schema'] = $schema;
        }

        if (null !== ($dataDir = $input->getOption('data-dir'))) {
            $options['dataDir'] = $dataDir;
        }

        if (null !== ($configSet = $input->getOption('config-set'))) {
            $options['configSet'] = $configSet;
        }

        if (null !== ($collection = $input->getOption('collection'))) {
            $options['collection'] = $collection;
        }

        if (null !== ($shard = $input->getOption('shard'))) {
            $options['shard'] = $shard;
        }

        if (null !== ($async = $input->getOption('async'))) {
            $options['async'] = $async;
        }

        return $options;
    }
}
