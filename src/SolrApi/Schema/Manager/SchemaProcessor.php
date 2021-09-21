<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Manager;

use Solrphp\SolariumBundle\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator;

/**
 * Schema Processor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaProcessor
{
    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema
     */
    private ManagedSchema $managedSchema;

    /**
     * @var iterable<\Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface>
     */
    private iterable $processors;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager
     */
    private SchemaManager $manager;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator
     */
    private SchemaNodeGenerator $generator;

    /**
     * @param iterable|\Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeProcessorInterface[] $processors
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager                               $manager
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator|null                  $generator
     */
    public function __construct($processors, SchemaManager $manager, SchemaNodeGenerator $generator = null)
    {
        $this->processors = $processors;
        $this->manager = $manager;
        $this->generator = $generator ?? new SchemaNodeGenerator();
    }

    /**
     * @param string $core
     *
     * @return $this
     */
    public function withCore(string $core): self
    {
        $this->manager->setCore($core);

        return $this;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema $managedSchema
     *
     * @return $this
     */
    public function withSchema(ManagedSchema $managedSchema): self
    {
        $this->managedSchema = $managedSchema;

        return $this;
    }

    /**
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function process(): void
    {
        foreach ($this->generator->get($this->managedSchema) as $configNode) {
            foreach ($this->processors as $processor) {
                if (false === $processor->supports($configNode)) {
                    continue;
                }

                $processor->setManager($this->manager)->process($configNode);
            }
        }

        try {
            $this->manager->persist();
        } catch (\JsonException $e) {
            throw new ProcessorException('unable to persist managed schema', $e);
        }

        $this->manager->flush();
    }
}
