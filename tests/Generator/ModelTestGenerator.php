<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Generator;

use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;
use Laminas\Code\Generator\ValueGenerator;
use Laminas\Code\Reflection\MethodReflection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Tests\Helper\Accessor;
use Solrphp\SolariumBundle\Tests\Helper\Dummy;
use Solrphp\SolariumBundle\Tests\Helper\RefClass;
use Solrphp\SolariumBundle\Tests\Helper\Value;
use Symfony\Component\Finder\Finder;

/**
 * Model Test Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ModelTestGenerator
{
    /**
     * @var array|string[]
     */
    private static array $nsFragments = [
        'Solrphp',
        'SolariumBundle',
        'Tests',
        'Unit',
    ];

    private string $fileName;
    private string $normalizedName;
    private string $fileNamespace;
    private string $testNamespace;
    private string $testFilePath = __DIR__.'/../../tests/Unit/';

    /**
     * @param string $name
     *
     * @throws \RuntimeException
     */
    public function __construct(string $name)
    {
        global $argv;

        $this->fileName = $name;
        $this->prepare();
    }

    /**
     * @return int
     *
     * @throws \Laminas\Code\Generator\Exception\RuntimeException
     * @throws \Roave\BetterReflection\Reflector\Exception\IdentifierNotFound
     */
    public function generate(): int
    {
        $file = FileGenerator::fromArray([
            'filename' => sprintf('%s/%sTest.php', $this->testFilePath, $this->fileName),
            'namespace' => $this->testNamespace,
            'uses' => $this->uses(),
            'declares' => [
                'strict_types' => 1,
            ],
        ]);

        $class = ClassGenerator::fromArray([
            'name' => sprintf('%sTest', $this->fileName),
            'flags' => ClassGenerator::FLAG_FINAL,
            'extendedclass' => new ValueGenerator('TestCase', PropertyValueGenerator::TYPE_CONSTANT),
        ]);

        $class
            ->setDocBlock($this->docblock())
            ->addProperties([
                PropertyGenerator::fromArray([
                    'name' => 'class',
                    'visibility' => PropertyGenerator::VISIBILITY_PRIVATE,
                    'defaultvalue' => $this->fileNamespace.'\\'.$this->fileName,
                    'static' => true,
                ]),
                PropertyGenerator::fromArray([
                    'name' => 'values',
                    'visibility' => PropertyGenerator::VISIBILITY_PRIVATE,
                    'defaultvalue' => Dummy::properties($this->fileNamespace.'\\'.$this->fileName, true, false),
                ]),
                PropertyGenerator::fromArray([
                    'name' => 'nonNullable',
                    'visibility' => PropertyGenerator::VISIBILITY_PRIVATE,
                    'defaultvalue' => Dummy::properties($this->fileNamespace.'\\'.$this->fileName, false, false),
                    'static' => true,
                ]),
                PropertyGenerator::fromArray([
                    'name' => 'accessors',
                    'visibility' => PropertyGenerator::VISIBILITY_PRIVATE,
                    'defaultvalue' => Accessor::all($this->fileNamespace.'\\'.$this->fileName),
                    'static' => true,
                ]),
            ])
            ->addMethods([
                $this->readWriteTestMethod(),
                $this->valueMethod(),
            ])
        ;

        $file
            ->setClass($class)
            ->write()
        ;

        return 0;
    }

    /**
     * @return \Laminas\Code\Generator\MethodGenerator
     */
    private function readWriteTestMethod(): MethodGenerator
    {
        $method = MethodGenerator::fromReflection(new MethodReflection(ModelTestTemplate::class, 'testReadWritePropertiesMethods'));

        $method->setName(sprintf('test%sReadWritePropertiesMethods', $this->fileName));

        return $method;
    }

    /**
     * @return \Laminas\Code\Generator\MethodGenerator
     */
    private function valueMethod(): MethodGenerator
    {
        return MethodGenerator::fromReflection(new MethodReflection(ModelTestTemplate::class, 'value'));
    }

    /**
     * @return \Laminas\Code\Generator\DocBlockGenerator
     */
    private function docblock(): DocBlockGenerator
    {
        return DocBlockGenerator::fromArray([
            'shortDescription' => sprintf('%s Test.', $this->normalizedName),
            'tags' => [
                [
                    'name' => 'author',
                    'description' => 'wicliff <wicliff.wolda@gmail.com>',
                ],
            ],
        ]);
    }

    /**
     * @return array
     */
    private function uses(): array
    {
        $fqns = RefClass::composites($this->fileNamespace.'\\'.$this->fileName);

        $uses = [];
        $uses[] = [TestCase::class, null];
        $uses[] = [Value::class, null];

        foreach ($fqns as $fqn) {
            $uses[] = [\get_class($fqn), null];
        }

        return $uses;
    }

    /**
     * @throws \RuntimeException
     */
    private function prepare(): void
    {
        $finder = new Finder();

        foreach ($finder->in(__DIR__.'/../../src')->name(sprintf('%s.php', $this->fileName))->files() as $file) {
            if (0 === preg_match('/namespace ([^;]+);/', $file->getContents(), $matches)) {
                throw new \RuntimeException(sprintf('no namespace found for %s', $this->fileName));
            }

            $this->fileNamespace = $matches[1];
            $this->testNamespace = implode('\\', array_merge(self::$nsFragments, \array_slice(explode('\\', $matches[1]), 2)));
            $this->testFilePath = $this->testFilePath.implode('/', \array_slice(explode('\\', $matches[1]), 2));

            if (!is_dir($this->testFilePath)) {
                mkdir($this->testFilePath, 0755, true);
            }
            $this->testFilePath = realpath($this->testFilePath);
        }

        $this->normalizedName = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $this->fileName);
    }
}
