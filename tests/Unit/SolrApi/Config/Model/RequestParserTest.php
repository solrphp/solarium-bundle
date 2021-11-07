<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Model;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Model\RequestParser;
use Solrphp\SolariumBundle\Tests\Helper\ArrayHelper;

/**
 * Request Parser Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class RequestParserTest extends TestCase
{
    /**
     * @var class-string
     */
    private static string $class = RequestParser::class;

    /**
     * @var array<string, bool|int>
     */
    private array $values = [
        'enableRemoteStreaming' => false,
        'enableStreamBody' => false,
        'multipartUploadLimitInKB' => 3,
        'formdataUploadLimitInKB' => 1,
        'addHttpRequestToContext' => false,
    ];

    /**
     * @var array<string, bool|int>
     */
    private static $nonNullable = [];

    /**
     * @var array<string, array<string, string|null>>
     */
    private static $accessors = [
        'enableRemoteStreaming' => [
            'reader' => 'getEnableRemoteStreaming',
            'writer' => 'setEnableRemoteStreaming',
            'remover' => null,
        ],
        'enableStreamBody' => [
            'reader' => 'getEnableStreamBody',
            'writer' => 'setEnableStreamBody',
            'remover' => null,
        ],
        'multipartUploadLimitInKB' => [
            'reader' => 'getMultipartUploadLimitInKB',
            'writer' => 'setMultipartUploadLimitInKB',
            'remover' => null,
        ],
        'formdataUploadLimitInKB' => [
            'reader' => 'getFormdataUploadLimitInKB',
            'writer' => 'setFormdataUploadLimitInKB',
            'remover' => null,
        ],
        'addHttpRequestToContext' => [
            'reader' => 'getAddHttpRequestToContext',
            'writer' => 'setAddHttpRequestToContext',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRequestParserReadWritePropertiesMethods(): void
    {
        $instance = new self::$class();

        self::assertInstanceOf(\JsonSerializable::class, $instance);

        foreach ($this->values as $name => $value) {
            $reader = self::$accessors[$name]['reader'];
            $writer = self::$accessors[$name]['writer'];
            $remover = self::$accessors[$name]['remover'];

            $this->values[$name] = $value = $this->value($value);
            $instance->$writer($value);

            if (null === $remover) {
                self::assertSame($value, $instance->$reader());
            } else {
                self::assertContains($value, $instance->$reader());
            }
        }

        self::assertSame(array_keys(ArrayHelper::keysort($this->values)), array_keys(ArrayHelper::keysort($instance->jsonSerialize())));

        foreach (self::$accessors as $property => $accessors) {
            if (null === $remover = $accessors['remover']) {
                continue;
            }

            $reader = $accessors['reader'];

            self::assertTrue($instance->$remover($this->values[$property]));
            self::assertNotContains($this->values[$property], $instance->$reader());
            self::assertFalse($instance->$remover($this->values[$property]));
        }

        $instance = new self::$class();

        if (0 !== \count(self::$nonNullable)) {
            foreach (self::$nonNullable as $name => $value) {
                $writer = self::$accessors[$name]['writer'];
                $value = $this->value($value);
                $instance->$writer($value);
            }

            self::assertSame(array_keys(ArrayHelper::keysort(self::$nonNullable)), array_keys(ArrayHelper::keysort($instance->jsonSerialize())));
        } else {
            self::assertEmpty($instance->jsonSerialize());
        }
    }

    /**
     * @param string|int|float|bool|iterable<int|string, mixed> $value
     *
     * @return object|string|int|float|bool|iterable<int|string, mixed>
     */
    private function value($value)
    {
        return \is_string($value) && class_exists($value) ? new $value() : $value;
    }
}
