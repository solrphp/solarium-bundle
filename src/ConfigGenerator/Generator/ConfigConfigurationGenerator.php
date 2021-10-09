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
final class ConfigConfigurationGenerator extends AbstractConfigurationGenerator implements ConfigurationGeneratorInterface
{
    public const TYPE_UPDATE_HANDLER = 'update_handler';
    public const TYPE_QUERY = 'query';
    public const TYPE_REQUEST_DISPATCHER = 'request_dispatcher';
    public const TYPE_REQUEST_HANDLER = 'request_handlers';
    public const TYPE_SEARCH_COMPONENT = 'search_components';

    public static string $nodeName = 'solr_configs';

    public static array $nodeTypes = [
        self::TYPE_UPDATE_HANDLER,
        self::TYPE_QUERY,
        self::TYPE_REQUEST_DISPATCHER,
        self::TYPE_REQUEST_HANDLER,
        self::TYPE_SEARCH_COMPONENT,
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
