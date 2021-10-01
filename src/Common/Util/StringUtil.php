<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Util;

/**
 * StringUtil.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StringUtil
{
    public const FORMAT_XML = 'xml';
    public const FORMAT_JSON = 'json';

    private const JSON_DEPTH = 512;

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param string|null $data
     * @param string      $format
     *
     * @return string
     */
    public static function prettify(?string $data, string $format): string
    {
        if (str_ends_with($format, self::FORMAT_XML)) {
            return self::prettyXML($data);
        }

        if (self::FORMAT_JSON === $format) {
            return self::prettyJson($data);
        }

        return $data ?? '';
    }

    /**
     * @param string|null $string
     *
     * @return string
     */
    public static function prettyJson(?string $string): string
    {
        if (null === $string) {
            return '';
        }

        try {
            return json_encode(json_decode($string, true, self::JSON_DEPTH, \JSON_THROW_ON_ERROR), \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT | \JSON_PRESERVE_ZERO_FRACTION | \JSON_UNESCAPED_SLASHES);
        } catch (\JsonException $e) {
            return $string;
        }
    }

    /**
     * @param string|null $string
     *
     * @return string
     */
    public static function prettyXml(?string $string): string
    {
        if (null === $string) {
            return '';
        }

        $doc = new \DOMDocument('1.0');

        if (false === @$doc->loadXML($string)) {
            return $string;
        }

        $doc->formatOutput = true;

        return $doc->saveXML();
    }
}
