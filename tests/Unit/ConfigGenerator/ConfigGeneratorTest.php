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
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Server\Api\Query;
use Solrphp\SolariumBundle\ConfigGenerator\ConfigGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Dumper\PhpDumper;
use Solrphp\SolariumBundle\ConfigGenerator\Dumper\YamlDumper;
use Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\CopyFieldGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\DynamicFieldGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\FieldGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\FieldTypeGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\CharFilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\FilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\TokenizerFieldTypeVisitor;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * ConfigGeneratorTest.
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

        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();

        $generator = new ConfigGenerator([], [], 'foo', $client);
        $generator->withExtension('xml');
    }

    /**
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testGenerate(): void
    {
        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $client->expects(self::once())
            ->method('execute')
            ->willReturn($this->getResult())
        ;

        (new ConfigGenerator($this->getHandlers(1, 1, 1, 1), $this->getDumpers(), __DIR__, $client))
            ->withExtension(DumperInterface::EXTENSION_YAML)
            ->withCore('foo')
            ->generate()
        ;

        // cleanup
        unlink(__DIR__.'/solrphp_solarium.yaml');
    }

    /**
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testTypesGenerate(): void
    {
        $options = [
            'version' => 'v1',
            'method' => 'GET',
            'resultclass' => 'Solarium\Core\Query\Result\QueryType',
            'handler' => 'foo/schema',
        ];

        $params = [
            'wt' => 'schema.xml',
        ];

        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([new Curl(), new EventDispatcher()])
            ->onlyMethods(['execute'])
            ->getMock()
        ;

        $client->expects(self::once())
            ->method('execute')
            ->with(
                self::callback(static function ($query) use ($options, $params) {
                    return $options === $query->getOptions() && $params === $query->getParams();
                }),
                self::callback(static function ($core) {
                    return 'foo' === $core;
                })
            )
            ->willReturn($this->getResult())
        ;

        (new ConfigGenerator($this->getHandlers(1, 0, 1, 0), $this->getDumpers(), __DIR__, $client))
            ->withExtension(DumperInterface::EXTENSION_YAML)
            ->withCore('foo')
            ->withTypes([ConfigGenerator::TYPE_COPY_FIELD, ConfigGenerator::TYPE_DYNAMIC_FIELD])
            ->generate()
        ;

        // cleanup
        unlink(__DIR__.'/solrphp_solarium.yaml');
    }

    /**
     * @return \Solarium\Core\Query\Result\Result
     *
     * @throws \Solarium\Exception\HttpException
     */
    public function getResult(): Result
    {
        return new Result(new Query(), new Response($this->getSchemaXml(), ['HTTP 200 OK']));
    }

    /**
     * @param int $copies
     * @param int $fields
     * @param int $dynamics
     * @param int $types
     *
     * @return array
     *
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    private function getHandlers(int $copies, int $fields, int $dynamics, int $types): array
    {
        $copyFieldGenerator = $this->getMockBuilder(CopyFieldGeneratorHandler::class)->onlyMethods(['handle'])->getMock();
        $copyFieldGenerator->expects(self::exactly($copies))->method('handle');

        $fieldGenerator = $this->getMockBuilder(FieldGeneratorHandler::class)->onlyMethods(['handle'])->getMock();
        $fieldGenerator->expects(self::exactly($fields))->method('handle');

        $dynamicFieldGenerator = $this->getMockBuilder(DynamicFieldGeneratorHandler::class)->onlyMethods(['handle'])->getMock();
        $dynamicFieldGenerator->expects(self::exactly($dynamics))->method('handle');

        $fieldTypeGenerator = $this->getMockBuilder(FieldTypeGeneratorHandler::class)->setConstructorArgs([$this->getVisitors()])->onlyMethods(['handle'])->getMock();
        $fieldTypeGenerator->expects(self::exactly($types))->method('handle');

        return [
            $copyFieldGenerator,
            $fieldGenerator,
            $dynamicFieldGenerator,
            $fieldTypeGenerator,
        ];
    }

    /**
     * @return array<\Solrphp\SolariumBundle\Contract\ConfigGenerator\FieldTypeVisitorInterface>
     */
    private function getVisitors(): array
    {
        return [
            new CharFilterFieldTypeVisitor(),
            new FilterFieldTypeVisitor(),
            new TokenizerFieldTypeVisitor(),
        ];
    }

    /**
     * @return array<DumperInterface>
     */
    private function getDumpers(): array
    {
        return [
            new YamlDumper(),
            new PhpDumper(),
        ];
    }

    /**
     * @return string
     */
    private function getSchemaXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<schema name="default-config" version="1.6">
  <uniqueKey>id</uniqueKey>
  <field name="id" type="string" multiValued="false" indexed="true" required="true" stored="true"/>
  <field name="inStock" type="booleans"/>
  <field name="includes" type="text_general"/>
  <dynamicField name="*_str" type="strings" docValues="true" indexed="false" stored="false" useDocValuesAsStored="false"/>
  <dynamicField name="*_d" type="pdouble" indexed="true" stored="true"/>
  <dynamicField name="*_p" type="location" indexed="true" stored="true"/>
  <copyField source="features" dest="features_str" maxChars="256"/>
  <copyField source="name" dest="name_str" maxChars="256"/>
  <copyField source="cat" dest="cat_str" maxChars="256"/>
</schema>
XML;
    }
}
