<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Generator;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigurationGeneratorInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\AbstractConfigurationGenerator;

/**
 * Stub Configuration Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StubConfigurationGenerator extends AbstractConfigurationGenerator implements ConfigurationGeneratorInterface
{
    /**
     * @var string[]
     */
    private array $types = [];

    /**
     * {@inheritDoc}
     */
    public function generate(string $core, array $types): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getNodes(): ?array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeName(): string
    {
        return 'foo';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param string[] $types
     */
    public function setTypes(array $types): void
    {
        $this->types = $types;
    }
}
