<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Param;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\ParamGeneratorHandlerInterface;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ParamConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor\AppendsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor\InvariantsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor\ParametersVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * ParameterSet Map Generator Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ParameterSetMapGeneratorHandler implements ParamGeneratorHandlerInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'name',
    ];
    /**
     * @var string
     */
    private static string $root = '//lst[@name="response"]/lst[@name="params"]/lst';

    /**
     * @var iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ParamGeneratorVisitorInterface>
     */
    private iterable $visitors;

    /**
     * @param iterable<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ParamGeneratorVisitorInterface>|null $visitors
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
        $nodes = [];

        $crawler->filterXPath(self::$root)->each(function (Crawler $crawler) use ($closure, &$nodes) {
            // this inspection is disabled as the crawler's extract method will not return another array as the one fed
            /* @infection-ignore-all */
            if (false === $combined = @array_combine(self::$attributes, $crawler->extract(self::$attributes))) {
                return; // @codeCoverageIgnore
            }

            $node = $closure(ArrayUtil::filter($combined));

            foreach ($this->visitors as $visitor) {
                $visitor->visit($crawler, $closure, $node);
            }

            $nodes[] = ArrayUtil::filter($node);
        });

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP === $type;
    }

    /**
     * @return array<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ParamGeneratorVisitorInterface>
     */
    private function getDefaultVisitors(): array
    {
        return [
            new ParametersVisitor(),
            new AppendsVisitor(),
            new InvariantsVisitor(),
        ];
    }
}
