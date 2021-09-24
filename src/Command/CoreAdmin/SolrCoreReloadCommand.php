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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solr Core Reload Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCoreReloadCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:reload';

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
            ->setDescription('reloads a solr core')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'solr core to reload'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->reload(['core' => $input->getArgument('core')]);

        if (0 !== $response->getResponseHeader()->getStatus()) {
            $error = null !== $response->getError() ? $response->getError()->getMessage() : '[unable to get error message]';

            $output->writeln(sprintf('<error>error reloading core %s: %s (%d)</error>', $input->getArgument('core'), $error, $response->getResponseHeader()->getStatus()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully reloaded core %s</info>', $input->getArgument('core')));

        return Command::SUCCESS;
    }
}
