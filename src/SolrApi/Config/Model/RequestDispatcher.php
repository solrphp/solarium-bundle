<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Config\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * RequestDispatcher.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestDispatcher implements \JsonSerializable
{
    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     */
    private ?bool $handleSelect = null;

    /**
     * @var \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestParser|null
     */
    private ?RequestParser $requestParsers = null;

    /**
     * @return bool|null
     */
    public function getHandleSelect(): ?bool
    {
        return $this->handleSelect;
    }

    /**
     * @param bool|null $handleSelect
     */
    public function setHandleSelect(?bool $handleSelect): void
    {
        $this->handleSelect = $handleSelect;
    }

    /**
     * @return \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestParser|null
     */
    public function getRequestParsers(): ?RequestParser
    {
        return $this->requestParsers;
    }

    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Config\Model\RequestParser|null $requestParsers
     */
    public function setRequestParsers(?RequestParser $requestParsers): void
    {
        $this->requestParsers = $requestParsers;
    }

    /**
     * @return array<string, bool|RequestParser>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'handleSelect' => $this->handleSelect,
                'requestParsers' => $this->requestParsers,
            ],
            static function ($var) {
                return null !== $var;
            }
        );
    }
}
