<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Command\Schema;

use Solrphp\SolariumBundle\Common\Util\ErrorUtil;
use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaProcessor;
use Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Solr Schema Update Command.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrSchemaUpdateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'solr:schema:update';

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore
     */
    private SolrConfigurationStore $configurationStore;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaProcessor
     */
    private SchemaProcessor $processor;

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaProcessor $processor
     * @param \Solrphp\SolariumBundle\SolrApi\SolrConfigurationStore         $configurationStore
     */
    public function __construct(SchemaProcessor $processor, SolrConfigurationStore $configurationStore)
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
            ->setDescription('Modifies solr managed schema xml for given core')
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

        if (null === $schema = $this->configurationStore->getSchemaForCore($core)) {
            $output->writeln(sprintf('<error>No managed schema found for %s core</error>', $core));

            return Command::INVALID;
        }

        try {
            $this->processor
                ->withCore($core)
                ->withSchema($schema)
                ->process()
            ;
        } catch (ProcessorException $e) {
            $output->writeln(sprintf('<error>Unable to process managed schema for %s core: %s</error>', $core, ErrorUtil::fromSolrphpException($e, $output->getVerbosity())));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
