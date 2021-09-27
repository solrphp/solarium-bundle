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
class SolrCoreRenameCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:rename';

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
                new InputArgument('core', InputArgument::REQUIRED, 'name of the solr core to be renamed'),
                new InputArgument('other', InputArgument::REQUIRED, 'new name for the solr core'),
                new InputOption('async', null, InputOption::VALUE_REQUIRED, 'request id to track this action which will be processed asynchronously'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->rename($this->getOptions($input));

        if (0 !== $response->getResponseHeader()->getStatus()) {
            $output->writeln(sprintf('<error>error renaming core %s: %s</error>', $input->getArgument('core'), ErrorUtil::fromResponse($response, $output->getVerbosity())));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully renamed core %s to %s</info>', $input->getArgument('core'), $input->getArgument('other')));

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
            'other' => $input->getArgument('other'),
            'async' => $input->getOption('async'),
        ];
    }
}
