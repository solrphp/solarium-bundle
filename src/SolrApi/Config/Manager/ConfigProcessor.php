<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Manager;

use JsonException;
use Solarium\Exception\HttpException;
use Solrphp\SolariumBundle\Common\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigNodeGenerator;

/**
 * Config Processor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigProcessor
{
    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig
     */
    private SolrConfig $config;

    /**
     * @var iterable<\Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface>
     */
    private iterable $handlerChain;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager
     */
    private ConfigManager $manager;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigNodeGenerator
     */
    private ConfigNodeGenerator $generator;

    /**
     * @param iterable<\Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface> $handlers
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Manager\ConfigManager                          $manager
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Generator\ConfigNodeGenerator|null             $generator
     */
    public function __construct(iterable $handlers, ConfigManager $manager, ConfigNodeGenerator $generator = null)
    {
        $this->handlerChain = $handlers;
        $this->manager = $manager;
        $this->generator = $generator ?? new ConfigNodeGenerator();
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
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig $config
     *
     * @return $this
     */
    public function withConfig(SolrConfig $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @throws \Solrphp\SolariumBundle\Common\Exception\ProcessorException
     */
    public function process(): void
    {
        foreach ($this->generator->get($this->config) as $configNode) {
            foreach ($this->handlerChain as $handler) {
                if (false === $handler->supports($configNode)) {
                    continue;
                }

                $handler->setManager($this->manager)->handle($configNode);
            }
        }

        try {
            $result = $this->manager->persist();
        } catch (JsonException|HttpException $e) {
            throw new ProcessorException('unable to persist configuration', $e);
        }

        if (null === $result) {
            return;
        }

        $this->manager->flush();
    }
}
