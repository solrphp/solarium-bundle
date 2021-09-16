<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Schema\Model;

use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;
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
class SchemaModelsTest extends TestCase
{
    private static string $path = __DIR__.'/../../../../../src/SolrApi/Schema/Model';

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
    public function testSchemaModelPropertyWriterAccessibility(string $class): void
    {
        $refClass = (new BetterReflection())->classReflector()->reflect($class);
        $object = ObjectUtil::reflect(new $class());

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            $writer = Accessor::writer($property);
            $reader = Accessor::reader($property);

            self::assertIsCallable([$object, $writer]);

            $value = Dummy::getValue($property);
            $object->$writer($value);

            self::assertTrue(Value::validate($property, $value), sprintf('value of property %s from class %s is incorrect', $property->getName(), $class));

            $resolved = $object->$reader();

            if (is_iterable($resolved)) {
                self::assertContains($value, $resolved);

                $remover = Accessor::remover($property);
                $object->$remover($value);

                self::assertNotContains($value, $object->$reader());

                // re-delete should not throw anything
                $object->$remover($value);
            } else {
                self::assertSame($value, $object->$reader());
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
    public function testSchemaJsonSerialize(string $class, bool $includeNullable): void
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

            self::assertSame($object->$accessor(), $serialized[$normalised]);
        }

        self::assertSame(array_keys($properties), array_keys($serialized));

        // some config models are completely nullable. in order to prevent tests from failing,
        // this rather useless assertion is made.
        self::assertIsArray($serialized);
    }

    /**
     * @return \Generator<string, array>
     *
     * @throws \RuntimeException
     */
    public function nullableClassNameProvider(): \Generator
    {
        $finder = new Finder();

        foreach ($finder->in(self::$path)->notName('FieldPropertyTrait.php')->files() as $file) {
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

        foreach ($finder->in(self::$path)->notName('FieldPropertyTrait.php')->files() as $file) {
            if (0 === preg_match('/namespace ([^;]+);/', $file->getContents(), $matches)) {
                continue;
            }

            yield $file->getFilenameWithoutExtension() => [sprintf('%s\\%s', $matches[1], $file->getFilenameWithoutExtension())];
        }
    }
}
