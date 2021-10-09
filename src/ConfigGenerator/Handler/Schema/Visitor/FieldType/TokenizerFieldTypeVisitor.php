<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Handler\Schema\Visitor\FieldType;

use Solrphp\SolariumBundle\Common\Util\ArrayUtil;
use Solrphp\SolariumBundle\ConfigGenerator\Contract\SchemaGeneratorVisitorInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Tokenizer FieldType Visitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class TokenizerFieldTypeVisitor implements SchemaGeneratorVisitorInterface
{
    /**
     * @var array|string[]
     */
    public static array $attributes = [
        'name',
        'class',
        'mode',
        'filter',
        'mapping',
        'pattern',
        'replacement',
    ];

    /**
     * {@inheritdoc}
     */
    public function visit(Crawler $crawler, \Closure $closure, array &$analyzer): void
    {
        foreach ($crawler->filterXPath('//tokenizer')->extract(self::$attributes) as $filter) {
            if (false === $combined = @array_combine(self::$attributes, $filter)) {
                continue;
            }

            $analyzer['tokenizer'] = $closure(ArrayUtil::filter($combined));
        }
    }
}
