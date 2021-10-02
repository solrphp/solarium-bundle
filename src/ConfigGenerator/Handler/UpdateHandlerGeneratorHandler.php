<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\AutoCommitVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\AutoSoftCommitVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\CommitWithinVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\UpdateLogVisitor;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * UpdateHandlerGeneratorHandler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UpdateHandlerGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'class',
        'versionBucketLockTimeoutMs',
    ];

    /**
     * @var iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>
     */
    private iterable $visitors;

    /**
     * @param iterable<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>|null $visitors
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

        foreach ($crawler->filterXPath('//config/updateHandler')->extract(self::$attributes) as $updateHandler) {
            if (false === $combined = @array_combine(self::$attributes, $updateHandler)) {
                continue;
            }

            $node = $closure(ArrayUtil::filter($combined));

            foreach ($this->visitors as $visitor) {
                $visitor->visit($crawler, $closure, $node);
            }

            $nodes[] = ArrayUtil::filter($node);
        }

        return array_shift($nodes) ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return ConfigGenerator::TYPE_UPDATE_HANDLER === $type;
    }

    /**
     * @return array<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>
     */
    private function getDefaultVisitors(): array
    {
        return [
            new AutoCommitVisitor(),
            new AutoSoftCommitVisitor(),
            new CommitWithinVisitor(),
            new UpdateLogVisitor(),
        ];
    }
}
