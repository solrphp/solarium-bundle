<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Util;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Util\ConfigUtil;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;
use Solrphp\SolariumBundle\Tests\Helper\ObjectUtil;

/**
 * Config Util Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigUtilTest extends TestCase
{
    /**
     * @dataProvider propertyPathProvider
     *
     * @param string      $class
     * @param string|null $prefix
     * @param string|null $separator
     */
    public function testToPropertyPaths(string $class, ?string $prefix, ?string $separator): void
    {
        $query = ObjectUtil::reflect(new $class());
        $properties = ObjectUtil::properties(new $class());

        $paths = ConfigUtil::toPropertyPaths($query, $prefix, $separator);

        $separator = $separator ?? ConfigUtil::DEFAULT_SEPARATOR;

        foreach (array_keys($properties) as $property) {
            $key = $prefix ? sprintf('%s%s%s', $prefix, $separator, $property) : $property;

            self::assertArrayHasKey($key, $paths);
        }
    }

    /**
     * test composite proerty path.
     */
    public function testCompositePropertyPath(): void
    {
        $paths = ConfigUtil::toPropertyPaths(new Foo(), 'foo');

        self::assertArrayHasKey('foo.name', $paths);
        self::assertArrayHasKey('foo.bar.name', $paths);

        $fieldType = ObjectUtil::reflect(new FieldType());
        $paths = ConfigUtil::toPropertyPaths($fieldType, 'field_type');

        self::assertArrayHasKey('field_type.analyzers.0.class', $paths);
    }

    /**
     * @return \Generator
     */
    public function propertyPathProvider(): \Generator
    {
        yield 'default_separator' => [
            'class' => Field::class,
            'prefix' => 'field',
            'separator' => null,
        ];

        yield 'custom_separator' => [
            'class' => Field::class,
            'prefix' => 'field',
            'separator' => '|',
        ];

        yield 'no_prefix' => [
            'class' => Field::class,
            'prefix' => null,
            'separator' => null,
        ];
    }
}

// phpcs:disable
class Bar
{
    public string $name;

    public function __construct()
    {
        $this->name = 'bar';
    }
}

class Foo
{
    public string $name;
    public Bar $bar;

    public function __construct()
    {
        $this->name = 'foo';
        $this->bar = new Bar();
    }
}
// phpcs:enable
