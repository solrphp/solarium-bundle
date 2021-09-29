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

    public const TYPES = [
        self::TYPE_FIELD,
        self::TYPE_DYNAMIC_FIELD,
        self::TYPE_COPY_FIELD,
        self::TYPE_FIELD_TYPE,
    ];

    private static string $rootNode = 'solrphp_solarium';

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
    private array $types = self::TYPES;

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
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function generate(): void
    {
        $config = [];
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getSchemaXml());

        foreach ($this->types as $type) {
            foreach ($this->handlerChain as $handler) {
                if (false === $handler->supports($type)) {
                    continue;
                }

                $config[$type] = $handler->handle($crawler, $closure);
            }
        }

        $result = $this->dumperChain[$this->extension]->dump($config, self::$rootNode, $this->types);

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
}
