<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Command\Param;

use Solrphp\SolariumBundle\Common\Util\ErrorUtil;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solr Param Update Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrParamUpdateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:param:update';

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore
     */
    private SolrConfigurationStore $configurationStore;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamProcessor
     */
    private ParamProcessor $processor;

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamProcessor $processor
     * @param \Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore       $configurationStore
     */
    public function __construct(ParamProcessor $processor, SolrConfigurationStore $configurationStore)
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
            ->setDescription('modifies solr params.json for given core')
            ->setDefinition([
                new InputArgument('core', InputArgument::REQUIRED, 'solr core for which to update the schema'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $core = $input->getArgument('core');

        if (null === $params = $this->configurationStore->getParamsForCore($core)) {
            $output->writeln(sprintf('<error>No parameters found for %s core</error>', $core));

            return Command::INVALID;
        }

        try {
            $this->processor
                ->withCore($core)
                ->withRequestParameters($params)
                ->process()
            ;
        } catch (ProcessorException $e) {
            $output->writeln(sprintf('<error>Unable to process parameters for %s core: %s</error>', $core, ErrorUtil::fromSolrphpException($e, $output->getVerbosity())));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>Successfully updated parameters for %s core</info>', $core));

        return Command::SUCCESS;
    }
}
