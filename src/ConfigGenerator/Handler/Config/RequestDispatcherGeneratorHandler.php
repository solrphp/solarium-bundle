<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Config;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorHandlerInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ConfigConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestDispatcher\RequestParserVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * RequestDispatcherGeneratorHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class RequestDispatcherGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var string
     */
    private static string $root = '//config/requestDispatcher';

    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'handleSelect',
    ];

    /**
     * @var iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface>
     */
    private iterable $visitors;

    /**
     * @param iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface>|null $visitors
     */
    public function __construct(iterable $visitors = null)
    {
        $this->visitors = $visitors ?? $this->getDefaultVisitors();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        if (false === $combined = @array_combine(self::$attributes, $crawler->filterXPath(self::$root)->extract(self::$attributes))) {
            return [];
        }

        $node = $closure(ArrayUtil::filter($combined));

        foreach ($this->visitors as $visitor) {
            $visitor->visit($crawler, $closure, $node);
        }

        return $node;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        return ConfigConfigurationGenerator::TYPE_REQUEST_DISPATCHER === $type;
    }

    /**
     * @return \Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestDispatcher\RequestParserVisitor[]
     */
    private function getDefaultVisitors(): array
    {
        return [
            new RequestParserVisitor(),
        ];
    }
}
