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

use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\IndexTableCreator;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\StatusTableCreator;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\UserDataTableCreator;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SolrCore Status Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreStatusCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:status';

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager
     */
    private CoreManager $coreManager;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\StatusTableCreator
     */
    private StatusTableCreator $statusTableCreator;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\IndexTableCreator
     */
    private IndexTableCreator $indexTableCreator;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\UserDataTableCreator
     */
    private UserDataTableCreator $userDataTableCreator;

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Manager\CoreManager                    $coreManager
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\StatusTableCreator|null   $statusTableCreator
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\IndexTableCreator|null    $indexTableCreator
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table\UserDataTableCreator|null $userDataTableCreator
     */
    public function __construct(CoreManager $coreManager, StatusTableCreator $statusTableCreator = null, IndexTableCreator $indexTableCreator = null, UserDataTableCreator $userDataTableCreator = null)
    {
        parent::__construct();

        $this->coreManager = $coreManager;
        $this->statusTableCreator = $statusTableCreator ?? new StatusTableCreator();
        $this->indexTableCreator = $indexTableCreator ?? new IndexTableCreator();
        $this->userDataTableCreator = $userDataTableCreator ?? new UserDataTableCreator();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('get solr status')
            ->setDefinition([
                new InputOption('core', null, InputOption::VALUE_REQUIRED, 'retrieve the status of a specific core'),
                new InputOption('omit-index-info', null, InputOption::VALUE_NONE, 'won\'t return core index info'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->status($this->getOptions($input));

        if (!$response instanceof StatusResponse || 0 !== $response->getHeader()->getStatusCode()) {
            $error = null !== $response->getError() ? $response->getError()->getMessage() : '[unable to get error message]';

            $output->writeln(sprintf('<error>error while retrieving status: %s (%d)</error>', $error, $response->getHeader()->getStatusCode()));

            return Command::FAILURE;
        }

        $this->statusTableCreator->create($output, $response)->render();

        if (!$input->hasArgument('omit-index-info')) {
            $this->indexTableCreator->create($output, $response)->render();
            $this->userDataTableCreator->create($output, $response)->render();
        }

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

        if (null !== ($core = $input->getOption('core'))) {
            $options['core'] = $core;
        }

        if ($input->getOption('omit-index-info')) {
            $options['indexInfo'] = false;
        }

        return $options;
    }
}
