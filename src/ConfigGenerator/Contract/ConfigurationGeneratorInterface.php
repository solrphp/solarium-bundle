<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Contract;

/**
 * Configuration Generator Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ConfigurationGeneratorInterface
{
    /**
     * @param string   $core
     * @param string[] $types
     */
    public function generate(string $core, array $types): void;

    /**
     * @return array<string, mixed>
     */
    public function getNodes(): ?array;

    /**
     * @return string
     */
    public function getNodeName(): string;

    /**
     * @return string[]
     */
    public function getTypes(): array;
}
