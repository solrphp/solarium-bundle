<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Param\Manager;

use JsonException;
use Solarium\Exception\HttpException;
use Solrphp\SolariumBundle\Common\Exception\ProcessorException;
use Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters;
use Solrphp\SolariumBundle\SolrApi\Param\Generator\ParamsNodeGenerator;

/**
 * ParamProcessor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParamProcessor
{
    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters
     */
    private RequestParameters $requestParameters;

    /**
     * @var iterable<\Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface>
     */
    private iterable $handlerChain;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamManager
     */
    private ParamManager $manager;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Param\Generator\ParamsNodeGenerator
     */
    private ParamsNodeGenerator $generator;

    /**
     * @param iterable<\Solrphp\SolariumBundle\Contract\SolrApi\Manager\ConfigNodeHandlerInterface> $handlers
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Manager\ParamManager                            $manager
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Generator\ParamsNodeGenerator|null              $generator
     */
    public function __construct(iterable $handlers, ParamManager $manager, ParamsNodeGenerator $generator = null)
    {
        $this->handlerChain = $handlers;
        $this->manager = $manager;
        $this->generator = $generator ?? new ParamsNodeGenerator();
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
     * @param \Solrphp\SolariumBundle\SolrApi\Param\Config\RequestParameters $requestParameters
     *
     * @return $this
     */
    public function withRequestParameters(RequestParameters $requestParameters): self
    {
        $this->requestParameters = $requestParameters;

        return $this;
    }

    /**
     * @throws \Solrphp\SolariumBundle\Common\Exception\ProcessorException
     */
    public function process(): void
    {
        foreach ($this->generator->get($this->requestParameters) as $configNode) {
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
            throw new ProcessorException('unable to persist parameters', $e);
        }

        if (null === $result) {
            return;
        }

        $this->manager->flush();
    }
}
