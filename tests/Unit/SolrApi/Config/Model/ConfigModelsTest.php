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
use Roave\BetterReflection\BetterReflection;
use Solrphp\SolariumBundle\SolrApi\Config\Model\Property;
use Solrphp\SolariumBundle\Tests\Helper\Accessor;
use Solrphp\SolariumBundle\Tests\Helper\Dummy;
use Solrphp\SolariumBundle\Tests\Helper\ObjectUtil;
use Solrphp\SolariumBundle\Tests\Helper\RefClass;
use Solrphp\SolariumBundle\Tests\Helper\Value;
use Symfony\Component\Finder\Finder;

/**
 * Schema Models Test.
 *
 * test whether all properties have the appropriate accessors in order to prevent the ReflectionExtractor
 * from silently ignoring them during serialization.
 *
 * @author wicliff <wwolda@gmail.com>
 */
class ConfigModelsTest extends TestCase
{
    private static string $path = __DIR__.'/../../../../../src/SolrApi/Config/Model';
    private static array $properties = [
        'defaults',
        'appends',
        'invariants',
    ];

    /**
     * @dataProvider classNameProvider
     *
     * @param class-string $class
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \ReflectionException
     * @throws \Roave\BetterReflection\Reflector\Exception\IdentifierNotFound
     */
    public function testModelPropertyReaderAccessibility(string $class): void
    {
        $refClass = (new BetterReflection())->classReflector()->reflect($class);
        $instance = (new \ReflectionClass($class))->newInstanceWithoutConstructor();

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            $accessor = Accessor::reader($property);

            self::assertIsCallable([$instance, $accessor]);
        }
    }

    /**
     * @dataProvider classNameProvider
     *
     * @param class-string $class
     *
     * @throws \ReflectionException
     */
    public function testModelPropertyWriterAccessibility(string $class): void
    {
        $refClass = (new BetterReflection())->classReflector()->reflect($class);
        $instance = (new \ReflectionClass($class))->newInstanceWithoutConstructor();

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            $writer = Accessor::writer($property);
            $reader = Accessor::reader($property);

            self::assertIsCallable([$instance, $writer]);

            $value = Dummy::getValue($property);
            $instance->$writer($value);

            self::assertTrue(Value::validate($property, $value), sprintf('value of property %s from class %s is incorrect', $property->getName(), $class));

            $resolved = $instance->$reader();

            if (is_iterable($resolved)) {
                self::assertContains($value, $resolved);

                $remover = Accessor::remover($property);
                $instance->$remover($value);

                self::assertNotContains($value, $instance->$reader());

                // re-delete should not throw anything
                $instance->$remover($value);
            } else {
                self::assertSame($value, $instance->$reader());
            }
        }
    }

    /**
     * make sure all properties are taken into account during serialization.
     *
     * @dataProvider nullableClassNameProvider
     *
     * @param class-string $class
     * @param bool         $includeNullable
     */
    public function testJsonSerialize(string $class, bool $includeNullable): void
    {
        $object = ObjectUtil::reflect(new $class(), $includeNullable);
        $properties = RefClass::properties($class, $includeNullable);
        $serialized = $object->jsonSerialize();

        foreach ($properties as $name => $accessor) {
            // normalized array keys
            $normalised = !isset($serialized[$name]) ? strtolower(preg_replace('/(?<=[a-z])(?=[A-Z])/', '-', $name)) : $name;
            unset($properties[$name]);
            $properties[$normalised] = $accessor;

            self::assertArrayHasKey($normalised, $serialized);

            if (true === \in_array($name, self::$properties, true)) {
                self::assertSame($object->$accessor()[0]->jsonSerialize(), $serialized[$normalised]);
            } else {
                self::assertSame($object->$accessor(), $serialized[$normalised]);
            }
        }

        self::assertSame(array_keys($properties), array_keys($serialized));

        // some config models are completely nullable. in order to prevent tests from failing,
        // this rather useless assertion is made.
        self::assertIsArray($serialized);
    }

    /**
     * Test property serialization.
     */
    public function testPropertySerialization(): void
    {
        $field = new Property();
        $field->setName('foo');
        $field->setValue('bar');

        self::assertSame(['foo' => 'bar'], $field->jsonSerialize());
    }

    /**
     * @return \Generator<string, array>
     *
     * @throws \RuntimeException
     */
    public function nullableClassNameProvider(): \Generator
    {
        $finder = new Finder();

        foreach ($finder->in(self::$path)->notName('Property.php')->files() as $file) {
            if (0 === preg_match('/namespace ([^;]+);/', $file->getContents(), $matches)) {
                continue;
            }

            yield $file->getFilenameWithoutExtension() => [
                sprintf('%s\\%s', $matches[1], $file->getFilenameWithoutExtension()),
                true,
            ];

            yield $file->getFilenameWithoutExtension().'_nullified' => [
                sprintf('%s\\%s', $matches[1], $file->getFilenameWithoutExtension()),
                false,
            ];
        }
    }

    /**
     * @return \Generator<string, array>
     *
     * @throws \RuntimeException
     */
    public function classNameProvider(): \Generator
    {
        $finder = new Finder();

        foreach ($finder->in(self::$path)->notName('Property.php')->files() as $file) {
            if (0 === preg_match('/namespace ([^;]+);/', $file->getContents(), $matches)) {
                continue;
            }

            yield $file->getFilenameWithoutExtension() => [sprintf('%s\\%s', $matches[1], $file->getFilenameWithoutExtension())];
        }
    }
}
