<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Util;

/**
 * QueryUtil.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class QueryUtil
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param string        $root
     * @param array<string> $names
     *
     * @return string
     */
    public static function nodeNames(string $root, array $names): string
    {
        return sprintf('%s/*[%s]', $root, implode(' or ', array_map(static fn ($value) => sprintf('self::%s', $value), $names)));
    }

    /**
     * @param string        $root
     * @param string        $attribute
     * @param array<string> $values
     *
     * @return string
     */
    public static function attributeValues(string $root, string $attribute, array $values): string
    {
        return sprintf('%s/*[%s]', $root, implode(' or ', array_map(static fn ($value) => sprintf('@%s="%s"', $attribute, $value), $values)));
    }
}
