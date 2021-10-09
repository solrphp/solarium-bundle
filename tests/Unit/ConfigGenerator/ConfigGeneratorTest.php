<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException;
use Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\Generator\StubConfigurationGenerator;
use Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator\StubDumper;

/**
 * Config ConfigurationGenerator Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigGeneratorTest extends TestCase
{
    /**
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testExtensionException(): void
    {
        $this->expectException(GeneratorException::class);
        $this->expectExceptionMessage('dumping xml files is currently not supported');

        $generator = new ConfigGenerator([], [], 'foo');
        $generator->withExtension('xml');
    }

    /**
     * @dataProvider provideBeautify
     *
     * @param bool $bautify
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testGenerate(bool $bautify): void
    {
        $types = ['foo', 'bar'];
        $generator = $this->getMockedGenerator('foo', $types);

        $dumper = $this->getMockedDumper(1, [], 'foo', $types, $bautify);

        (new ConfigGenerator($generator, $dumper, __DIR__))
            ->withExtension('yaml')
            ->withCore('foo')
            ->withTypes($types)
            ->withBeautify($bautify)
            ->generate()
        ;

        // cleanup
        unlink(__DIR__.'/foo.yaml');
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testN0Generate(): void
    {
        $types = ['foo', 'bar'];
        $generator = $this->getMockedGenerator('bar', $types, null, 0, 0);

        $dumper = $this->getMockedDumper(0, [], 'foo', $types, true);

        (new ConfigGenerator($generator, $dumper, __DIR__))
            ->withExtension('yaml')
            ->withCore('bar')
            ->withTypes($types)
            ->withBeautify(true)
            ->generate()
        ;
    }

    /**
     * @return array<array<bool>>
     */
    public function provideBeautify(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param string        $core
     * @param string[]      $types
     * @param string[]|null $nodes
     * @param int           $getNodeName
     * @param int           $getTypes
     *
     * @return array<\Solrphp\SolariumBundle\ConfigGenerator\Contract\ConfigurationGeneratorInterface>
     *
     * @throws \PHPUnit\Framework\Exception
     */
    private function getMockedGenerator(string $core, array $types, ?array $nodes = [], int $getNodeName = 2, int $getTypes = 1): array
    {
        $generator = $this->getMockBuilder(StubConfigurationGenerator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generate', 'getNodes', 'getNodeName', 'getTypes'])
            ->getMock()
        ;

        $generator->expects(self::once())
            ->method('generate')
            ->with(
                $core,
                $types
            )
        ;

        $generator->expects(self::once())
            ->method('getNodes')
            ->willReturn($nodes)
        ;

        $generator->expects(self::exactly($getNodeName))
            ->method('getNodeName')
            ->willReturn('foo')
        ;

        $generator->expects(self::exactly($getTypes))
            ->method('getTypes')
            ->willReturn($types)
        ;

        return [$generator];
    }

    /**
     * @param int    $dump
     * @param array  $nodes
     * @param string $nodeName
     * @param array  $types
     * @param bool   $beautify
     *
     * @return array
     *
     * @throws \PHPUnit\Framework\Exception
     */
    private function getMockedDumper(int $dump, array $nodes, string $nodeName, array $types, bool $beautify): array
    {
        $dumper = $this->getMockBuilder(StubDumper::class)->onlyMethods(['dump'])->getMock();

        if (0 === $dump) {
            $dumper->expects(self::never())
                ->method('dump');

            return [$dumper];
        }

        $dumper->expects(self::exactly($dump))
            ->method('dump')
            ->with(
                $nodes,
                $nodeName,
                $types,
                $beautify
            )
            ->willReturn('')
        ;

        return [$dumper];
    }
}
