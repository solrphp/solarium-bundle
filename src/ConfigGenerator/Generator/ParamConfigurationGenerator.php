<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Generator;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigurationGeneratorInterface;

/**
 * Params Configuration Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ParamConfigurationGenerator extends AbstractConfigurationGenerator implements ConfigurationGeneratorInterface
{
    public const TYPE_PARAMETER_SET_MAP = 'parameter_set_maps';

    public static string $nodeName = 'parameters';

    public static array $nodeTypes = [
        self::TYPE_PARAMETER_SET_MAP,
    ];

    /**
     * @var string[]
     */
    private array $types = [];

    /**
     * {@inheritdoc}
     */
    public function setTypes(array $types): void
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeName(): string
    {
        return self::$nodeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
