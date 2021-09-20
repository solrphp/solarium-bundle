<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\SolrApi\Processor;

use Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface;

/**
 * ConfigNode Processor Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ConfigNodeProcessorInterface
{
    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface $configNode
     *
     * @return mixed
     *
     * @throws \Solrphp\SolariumBundle\Exception\ProcessorException
     */
    public function process(ConfigNodeInterface $configNode);

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Processor\ConfigNodeInterface $configNode
     *
     * @return bool
     */
    public function supports(ConfigNodeInterface $configNode): bool;

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface $manager
     *
     * @return $this
     */
    public function setManager(SolrApiManagerInterface $manager): self;
}
