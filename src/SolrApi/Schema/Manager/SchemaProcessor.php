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

use Solarium\Exception\HttpException;
use Solrphp\SolariumBundle\Common\Exception\ProcessorException;
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
     * @var iterable<\Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface>
     */
    private iterable $handlerChain;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager
     */
    private SchemaManager $manager;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator
     */
    private SchemaNodeGenerator $generator;

    /**
     * @param iterable|\Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface[] $handlers
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Manager\SchemaManager                           $manager
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Generator\SchemaNodeGenerator|null              $generator
     */
    public function __construct($handlers, SchemaManager $manager, SchemaNodeGenerator $generator = null)
    {
        $this->handlerChain = $handlers;
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
     * @throws \Solrphp\SolariumBundle\Common\Exception\ProcessorException
     */
    public function process(): void
    {
        foreach ($this->generator->get($this->managedSchema) as $configNode) {
            foreach ($this->handlerChain as $handler) {
                if (false === $handler->supports($configNode)) {
                    continue;
                }

                $handler->setManager($this->manager)->handle($configNode);
            }
        }

        try {
            $result = $this->manager->persist();
        } catch (\JsonException|HttpException $e) {
            throw new ProcessorException('unable to persist managed schema', $e);
        }

        if (null === $result) {
            return;
        }

        $this->manager->flush();
    }
}
