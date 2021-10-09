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
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestHandler\AppendsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestHandler\ComponentsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestHandler\DefaultsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestHandler\InvariantsVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * RequestHandlerGeneratorHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class RequestHandlerGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var array|string[]
     */
    private static array $attributes = [
        'name',
        'class',
        'useParams',
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
        $nodes = [];

        $crawler->filterXPath('//config/requestHandler')->each(function (Crawler $crawler) use ($closure, &$nodes) {
            /* @infection-ignore-all */
            if (false === $combined = @array_combine(self::$attributes, $crawler->extract(self::$attributes)[0])) {
                return;
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
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        return ConfigConfigurationGenerator::TYPE_REQUEST_HANDLER === $type;
    }

    /**
     * @return array<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface>
     */
    private function getDefaultVisitors(): array
    {
        return [
            new DefaultsVisitor(),
            new AppendsVisitor(),
            new InvariantsVisitor(),
            new ComponentsVisitor(),
        ];
    }
}
