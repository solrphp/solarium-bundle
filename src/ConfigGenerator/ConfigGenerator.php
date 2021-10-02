<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator;

use Solarium\Client;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Config Dumper.
 *
 * because we all hate writing those...
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigGenerator
{
    public const TYPE_FIELD = 'fields';
    public const TYPE_DYNAMIC_FIELD = 'dynamic_fields';
    public const TYPE_COPY_FIELD = 'copy_fields';
    public const TYPE_FIELD_TYPE = 'field_types';
    public const TYPE_UPDATE_HANDLER = 'update_handler';
    public const TYPE_QUERY = 'query';
    public const TYPE_REQUEST_DISPATCHER = 'request_dispatcher';
    public const TYPE_REQUEST_HANDLER = 'request_handlers';
    public const TYPE_SEARCH_COMPONENT = 'search_components';

    public const SCHEMA_TYPES = [
        self::TYPE_FIELD,
        self::TYPE_DYNAMIC_FIELD,
        self::TYPE_COPY_FIELD,
        self::TYPE_FIELD_TYPE,
    ];

    public const CONFIG_TYPES = [
        self::TYPE_UPDATE_HANDLER,
        self::TYPE_QUERY,
        self::TYPE_REQUEST_DISPATCHER,
        self::TYPE_REQUEST_HANDLER,
        self::TYPE_SEARCH_COMPONENT,
    ];

    private static string $rootNode = 'solrphp_solarium';
    private static string $schemaNode = 'managed_schemas';
    private static string $configNode = 'solr_configs';

    /**
     * @var iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface>
     */
    private iterable $handlerChain;

    /**
     * @var array<string, \Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface>
     */
    private array $dumperChain = [];

    /**
     * @var array<string>
     */
    private array $types;

    /**
     * @var string
     */
    private string $extension;

    /**
     * @var string
     */
    private string $projectDir;

    /**
     * @var \Solarium\Client
     */
    private Client $client;

    /**
     * @var string
     */
    private string $core;

    /**
     * @var bool
     */
    private bool $beautify = true;

    /**
     * @param iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface> $handlers
     * @param iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface>                 $dumpers
     * @param string                                                                                     $projectDir
     * @param \Solarium\Client                                                                           $client
     */
    public function __construct(iterable $handlers, iterable $dumpers, string $projectDir, Client $client)
    {
        $this->handlerChain = $handlers;
        $this->projectDir = $projectDir;
        $this->client = $client;
        $this->types = array_merge(self::SCHEMA_TYPES, self::CONFIG_TYPES);

        foreach ($dumpers as $dumper) {
            $this->dumperChain[$dumper::getExtension()] = $dumper;
        }
    }

    /**
     * @param string $core
     *
     * @return $this
     */
    public function withCore(string $core): self
    {
        $this->core = $core;

        return $this;
    }

    /**
     * @param array<string> $types
     *
     * @return $this
     */
    public function withTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }

    /**
     * @param string $extension
     *
     * @return $this
     *
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function withExtension(string $extension): self
    {
        if (false === \in_array($extension, DumperInterface::EXTENSIONS, true)) {
            throw new GeneratorException(sprintf('dumping %s files is currently not supported', $extension));
        }

        $this->extension = $extension;

        return $this;
    }

    /**
     * @param bool $beautify
     *
     * @return $this
     */
    public function withBeautify(bool $beautify): self
    {
        $this->beautify = $beautify;

        return $this;
    }

    /**
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function generate(): void
    {
        $config = [];
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);

        $schemaCrawler = new Crawler($this->getSchemaXml());
        $configCrawler = new Crawler($this->getConfigXml());

        foreach ($this->types as $type) {
            $nodeName = true === \in_array($type, self::CONFIG_TYPES, true) ? self::$configNode : self::$schemaNode;
            $crawler = true === \in_array($type, self::CONFIG_TYPES, true) ? $configCrawler : $schemaCrawler;

            foreach ($this->handlerChain as $handler) {
                if (false === $handler->supports($type)) {
                    continue;
                }

                $config[$nodeName][$type] = $handler->handle($crawler, $closure);
            }
        }

        $result = $this->dumperChain[$this->extension]->dump($config, self::$rootNode, $this->types, $this->beautify);

        file_put_contents(sprintf('%s/%s.%s', $this->projectDir, self::$rootNode, $this->extension), $result);
    }

    /**
     * @return string
     */
    private function getSchemaXml(): string
    {
        $query = $this->client->createApi(
            [
                'handler' => sprintf('%s/schema', $this->core),
            ]
        )->addParam('wt', 'schema.xml');

        return $this->client->execute($query, $this->core)->getResponse()->getBody();
    }

    /**
     * @return string
     */
    private function getConfigXml(): string
    {
        $query = $this->client->createApi([
            'handler' => sprintf('%s/admin/file', $this->core),
        ])
            ->addParam('wt', 'xml')
            ->addParam('file', 'solrconfig.xml')
        ;

        return $this->client->execute($query, $this->core)->getResponse()->getBody();
    }
}
