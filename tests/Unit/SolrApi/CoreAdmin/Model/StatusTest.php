<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\CoreAdmin\Model;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Index;
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\Status;
use Solrphp\SolariumBundle\Tests\Helper\Value;

/**
 * Status Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class StatusTest extends TestCase
{
    /**
     * @var string
     */
    private static $class = Status::class;

    /**
     * @var array<string, string|int>
     */
    private array $values = [
        'name' => 'foo',
        'instanceDir' => 'foo',
        'dataDir' => 'foo',
        'config' => 'foo',
        'schema' => 'foo',
        'startTime' => \DateTime::class,
        'uptime' => 5,
        'index' => Index::class,
    ];

    /**
     * @var array<string, string|int>
     */
    private static array $nonNullable = [
        'name' => 'foo',
        'instanceDir' => 'foo',
        'dataDir' => 'foo',
        'config' => 'foo',
        'schema' => 'foo',
        'startTime' => \DateTime::class,
        'uptime' => 4,
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private static array $accessors = [
        'name' => [
            'reader' => 'getName',
            'writer' => 'setName',
            'remover' => null,
        ],
        'instanceDir' => [
            'reader' => 'getInstanceDir',
            'writer' => 'setInstanceDir',
            'remover' => null,
        ],
        'dataDir' => [
            'reader' => 'getDataDir',
            'writer' => 'setDataDir',
            'remover' => null,
        ],
        'config' => [
            'reader' => 'getConfig',
            'writer' => 'setConfig',
            'remover' => null,
        ],
        'schema' => [
            'reader' => 'getSchema',
            'writer' => 'setSchema',
            'remover' => null,
        ],
        'startTime' => [
            'reader' => 'getStartTime',
            'writer' => 'setStartTime',
            'remover' => null,
        ],
        'uptime' => [
            'reader' => 'getUptime',
            'writer' => 'setUptime',
            'remover' => null,
        ],
        'index' => [
            'reader' => 'getIndex',
            'writer' => 'setIndex',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testStatusReadWritePropertiesMethods(): void
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

        self::assertSame(array_keys(Value::keysort($this->values)), array_keys(Value::keysort($instance->jsonSerialize())));

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

            self::assertSame(array_keys(Value::keysort(self::$nonNullable)), array_keys(Value::keysort($instance->jsonSerialize())));
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
