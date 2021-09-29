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

use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * CopyField Handler.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CopyFieldGeneratorHandler implements ConfigGeneratorHandlerInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'source',
        'dest',
        'maxChars',
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(Crawler $crawler, \Closure $closure): array
    {
        $nodes = [];

        foreach ($crawler->filterXPath('//schema/copyField')->extract(self::$attributes) as $field) {
            if (false === $combined = @array_combine(self::$attributes, $field)) {
                continue;
            }

            $nodes[] = $closure(array_filter($combined));
        }

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return ConfigGenerator::TYPE_COPY_FIELD === $type;
    }
}
