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
use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Model\UserData;
use Solrphp\SolariumBundle\Tests\Helper\Value;

/**
 * Index Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class IndexTest extends TestCase
{
    /**
     * @var string
     */
    private static string $class = Index::class;

    /**
     * @var array<string, string|int|bool>
     */
    private array $values = [
        'numDocs' => 1,
        'maxDoc' => 2,
        'deletedDocs' => 5,
        'indexHeapUsageBytes' => 1,
        'version' => 1,
        'segmentCount' => 5,
        'current' => false,
        'hasDeletions' => false,
        'directory' => 'foo',
        'segmentsFile' => 'foo',
        'userData' => UserData::class,
        'lastModified' => \DateTime::class,
        'sizeInBytes' => 5,
        'size' => 'foo',
    ];

    /**
     * @var array<string, string|int|bool>
     */
    private static array $nonNullable = [
        'numDocs' => 5,
        'maxDoc' => 5,
        'deletedDocs' => 2,
        'indexHeapUsageBytes' => 3,
        'version' => 5,
        'segmentCount' => 5,
        'current' => false,
        'hasDeletions' => false,
        'directory' => 'foo',
        'segmentsFile' => 'foo',
        'userData' => UserData::class,
        'lastModified' => \DateTime::class,
        'sizeInBytes' => 4,
        'size' => 'foo',
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private static array $accessors = [
        'numDocs' => [
            'reader' => 'getNumDocs',
            'writer' => 'setNumDocs',
            'remover' => null,
        ],
        'maxDoc' => [
            'reader' => 'getMaxDoc',
            'writer' => 'setMaxDoc',
            'remover' => null,
        ],
        'deletedDocs' => [
            'reader' => 'getDeletedDocs',
            'writer' => 'setDeletedDocs',
            'remover' => null,
        ],
        'indexHeapUsageBytes' => [
            'reader' => 'getIndexHeapUsageBytes',
            'writer' => 'setIndexHeapUsageBytes',
            'remover' => null,
        ],
        'version' => [
            'reader' => 'getVersion',
            'writer' => 'setVersion',
            'remover' => null,
        ],
        'segmentCount' => [
            'reader' => 'getSegmentCount',
            'writer' => 'setSegmentCount',
            'remover' => null,
        ],
        'current' => [
            'reader' => 'isCurrent',
            'writer' => 'setCurrent',
            'remover' => null,
        ],
        'hasDeletions' => [
            'reader' => 'isHasDeletions',
            'writer' => 'setHasDeletions',
            'remover' => null,
        ],
        'directory' => [
            'reader' => 'getDirectory',
            'writer' => 'setDirectory',
            'remover' => null,
        ],
        'segmentsFile' => [
            'reader' => 'getSegmentsFile',
            'writer' => 'setSegmentsFile',
            'remover' => null,
        ],
        'userData' => [
            'reader' => 'getUserData',
            'writer' => 'setUserData',
            'remover' => null,
        ],
        'lastModified' => [
            'reader' => 'getLastModified',
            'writer' => 'setLastModified',
            'remover' => null,
        ],
        'sizeInBytes' => [
            'reader' => 'getSizeInBytes',
            'writer' => 'setSizeInBytes',
            'remover' => null,
        ],
        'size' => [
            'reader' => 'getSize',
            'writer' => 'setSize',
            'remover' => null,
        ],
    ];

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testIndexReadWritePropertiesMethods(): void
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
