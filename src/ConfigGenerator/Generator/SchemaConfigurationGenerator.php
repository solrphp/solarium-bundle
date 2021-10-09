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
 * Config ConfigurationGenerator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SchemaConfigurationGenerator extends AbstractConfigurationGenerator implements ConfigurationGeneratorInterface
{
    public const TYPE_FIELD = 'fields';
    public const TYPE_DYNAMIC_FIELD = 'dynamic_fields';
    public const TYPE_COPY_FIELD = 'copy_fields';
    public const TYPE_FIELD_TYPE = 'field_types';

    public static string $nodeName = 'managed_schemas';

    public static array $nodeTypes = [
        self::TYPE_FIELD,
        self::TYPE_DYNAMIC_FIELD,
        self::TYPE_COPY_FIELD,
        self::TYPE_FIELD_TYPE,
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
