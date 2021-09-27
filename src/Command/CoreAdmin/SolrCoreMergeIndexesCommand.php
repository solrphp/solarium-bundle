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
class SolrCoreMergeIndexesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:core:merge-indexes';

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
            ->setDescription('merges one or more indexes to another index')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'name of the target core/index'),
                new InputOption('index-dir', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'multi-valued, directories that would be merged'),
                new InputOption('src-core', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'multi-valued, source cores that would be merged'),
                new InputOption('async', null, InputOption::VALUE_REQUIRED, 'request id to track this action which will be processed asynchronously'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->coreManager->mergeIndexes($this->getOptions($input));

        if (0 !== $response->getResponseHeader()->getStatus()) {
            $output->writeln(sprintf('<error>error merging indexes for core %s: %s</error>', $input->getArgument('core'), ErrorUtil::fromResponse($response, $output->getVerbosity())));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully merged indexes for core %s</info>', $input->getArgument('core')));

        return Command::SUCCESS;
    }

    /**
     * solarium filters out null options:.
     *
     * @see vendor/solarium/solarium/src/Component/RequestBuilder/RequestParamsTrait.php:81
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string, string|array<string>>
     */
    private function getOptions(InputInterface $input): array
    {
        return [
            'core' => $input->getArgument('core'),
            'indexDir' => $input->getOption('index-dir'),
            'srcCore' => $input->getOption('src-core'),
            'async' => $input->getOption('async'),
        ];
    }
}
