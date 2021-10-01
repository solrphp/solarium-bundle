<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Param\Model;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Param\Model\Parameter;
use Solrphp\SolariumBundle\SolrApi\Param\Model\ParameterSetMap;
use Solrphp\SolariumBundle\Tests\Helper\Value;

/**
 * Parameter Set Map Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ParameterSetMapTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = ParameterSetMap::class;

    /**
     * @var array<string, string>
     */
    private array $values = [
        'name' => 'foo',
        'parameters' => Parameter::class,
        '_invariants_' => Parameter::class,
        '_appends_' => Parameter::class,
    ];

    /**
     * @var array<string, string>
     */
    private static array $nonNullable = [
        'name' => 'foo',
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
        'parameters' => [
            'reader' => 'getParameters',
            'writer' => 'addParameter',
            'remover' => 'removeParameter',
        ],
        '_invariants_' => [
            'reader' => 'getInvariants',
            'writer' => 'addInvariant',
            'remover' => 'removeInvariant',
        ],
        '_appends_' => [
            'reader' => 'getAppends',
            'writer' => 'addAppend',
            'remover' => 'removeAppend',
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testParameterSetMapReadWritePropertiesMethods(): void
    {
        $instance = new self::$class();

        self::assertInstanceOf(\JsonSerializable::class, $instance);

        foreach ($this->values as $name => $value) {
            $reader = self::$accessors[$name]['reader'];
            $writer = self::$accessors[$name]['writer'];
            $remover = self::$accessors[$name]['remover'];

            try {
                $value = $this->value($value);
            } catch (\Exception $e) {
                $value = new Parameter('foo', 'qux');
            }

            $this->values[$name] = $value;
            $instance->$writer($value);

            if (null === $remover) {
                self::assertSame($value, $instance->$reader());
            } else {
                self::assertContains($value, $instance->$reader());
            }
        }

        self::assertArrayHasKey('_invariants_', Value::keysort($instance->jsonSerialize())['foo']);
        self::assertArrayHasKey('_appends_', Value::keysort($instance->jsonSerialize())['foo']);
        self::assertArrayNotHasKey('parameters', Value::keysort($instance->jsonSerialize())['foo']);

        foreach (self::$accessors as $property => $accessors) {
            if (null === $remover = $accessors['remover']) {
                continue;
            }

            $reader = $accessors['reader'];

            self::assertTrue($instance->$remover($this->values[$property]));
            self::assertNotContains($this->values[$property], $instance->$reader());
            self::assertFalse($instance->$remover($this->values[$property]));
        }

        if (0 === \count(self::$nonNullable)) {
            $instance = new self::$class();
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
        return \is_string($value) && class_exists($value) ? new $value('foo', 'bar') : $value;
    }
}
