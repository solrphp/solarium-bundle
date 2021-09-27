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
 * Solr Core Unload Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreUnloadCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:unload';

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
            ->setDescription('unload a solr core')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'solr core to unload'),
                new InputOption('delete-index', null, InputOption::VALUE_NONE, 'will remove the index when unloading the core'),
                new InputOption('delete-data-dir', null, InputOption::VALUE_NONE, 'removes the data directory and all sub-directories'),
                new InputOption('delete-instance-dir', null, InputOption::VALUE_NONE, 'removes everything related to the core, including the index directory, configuration files and other related files'),
                new InputOption('async', null, InputOption::VALUE_REQUIRED, 'request id to track this action which will be processed asynchronously'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->unload($this->getOptions($input));

        if (0 !== $response->getResponseHeader()->getStatus()) {
            $output->writeln(sprintf('<error>error unloading core %s: %s</error>', $input->getArgument('core'), ErrorUtil::fromResponse($response, $output->getVerbosity())));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully unloaded core %s</info>', $input->getArgument('core')));

        return Command::SUCCESS;
    }

    /**
     * solarium filters out null options:.
     *
     * @see vendor/solarium/solarium/src/Component/RequestBuilder/RequestParamsTrait.php:81
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string, bool|string>
     */
    private function getOptions(InputInterface $input): array
    {
        return [
            'core' => $input->getArgument('core'),
            'deleteIndex' => $input->getOption('delete-index'),
            'deleteDataDir' => $input->getOption('delete-data-dir'),
            'deleteInstanceDir' => $input->getOption('delete-instance-dir'),
            'async' => $input->getOption('async'),
        ];
    }
}
