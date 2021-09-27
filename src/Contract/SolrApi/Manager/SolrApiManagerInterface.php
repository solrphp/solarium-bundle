<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Contract\SolrApi\Manager;

use Solarium\Core\Query\Result\ResultInterface;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;

/**
 * SolrApi Manager Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface SolrApiManagerInterface
{
    /**
     * @param string $core
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
     */
    public function setCore(string $core): self;

    /**
     * @param string            $command
     * @param \JsonSerializable $data
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Manager\SolrApiManagerInterface
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    public function addCommand(string $command, \JsonSerializable $data): self;

    /**
     * @param string $path
     *
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     *
     * @throws \Solrphp\SolariumBundle\Exception\UnexpectedValueException
     */
    public function call(string $path): ResponseInterface;

    /**
     * returns null in case there's nothing to persist.
     *
     * @return \Solarium\Core\Query\Result\ResultInterface|null
     *
     * @throws \Solarium\Exception\HttpException
     * @throws \JsonException
     */
    public function persist(): ?ResultInterface;

    /**
     * @return \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface
     */
    public function flush(): ResponseInterface;
}
