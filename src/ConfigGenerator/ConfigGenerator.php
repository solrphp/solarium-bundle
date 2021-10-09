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

use Solrphp\SolariumBundle\ConfigGenerator\Contract\DumperInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException;

/**
 * Config Dumper.
 *
 * because we all hate writing those...
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigGenerator
{
    /**
     * @var iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigurationGeneratorInterface>
     */
    private iterable $generators;

    /**
     * @var array<string, \Solrphp\SolariumBundle\ConfigGenerator\Contract\DumperInterface>
     */
    private array $dumpers = [];

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
     * @var string
     */
    private string $core;

    /**
     * @var bool
     */
    private bool $beautify = true;

    /**
     * @param iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigurationGeneratorInterface> $generators
     * @param iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\DumperInterface>                 $dumpers
     * @param string                                                                                     $projectDir
     */
    public function __construct(iterable $generators, iterable $dumpers, string $projectDir)
    {
        $this->generators = $generators;

        foreach ($dumpers as $dumper) {
            $this->dumpers[$dumper::getExtension()] = $dumper;
        }

        $this->projectDir = $projectDir;
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
        foreach ($this->generators as $generator) {
            $generator->generate($this->core, $this->types);

            if (null !== $nodes = $generator->getNodes()) {
                $result = $this->dumpers[$this->extension]->dump($nodes, $generator->getNodeName(), $generator->getTypes(), $this->beautify);

                file_put_contents(sprintf('%s/%s.%s', $this->projectDir, $generator->getNodeName(), $this->extension), $result);
            }
        }
    }
}
