<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Config\Visitor\RequestDispatcher;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigGeneratorVisitorInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * RequestParserVisitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class RequestParserVisitor implements ConfigGeneratorVisitorInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'enableRemoteStreaming',
        'enableStreamBody',
        'multipartUploadLimitInKB',
        'formdataUploadLimitInKB',
        'addHttpRequestToContext',
    ];

    /**
     * @var string
     */
    private static string $root = '//config/requestDispatcher/requestParsers';

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$updateHandler): void
    {
        $nodes = [];

        foreach ($crawler->filterXPath(self::$root)->extract(self::$attributes) as $requestParser) {
            if (false === $combined = @array_combine(self::$attributes, $requestParser)) {
                continue;
            }

            $nodes[] = $closure(ArrayUtil::filter($combined));
        }

        if (\count($nodes)) {
            $updateHandler['request_parsers'] = $nodes;
        }
    }
}
