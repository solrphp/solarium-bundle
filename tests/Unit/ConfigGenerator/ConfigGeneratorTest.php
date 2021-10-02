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
use Solrphp\SolariumBundle\ConfigGenerator\Handler\QueryGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\RequestDispatcherGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\RequestHandlerGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\SearchComponentGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\UpdateHandlerGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\CharFilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\FilterFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\FieldType\TokenizerFieldTypeVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\Query\DocumentCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\Query\FieldValueCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\Query\FilterCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\Query\ResultCacheVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestDispatcher\RequestParserVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestHandler\AppendsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestHandler\ComponentsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestHandler\DefaultsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\RequestHandler\InvariantsVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\AutoCommitVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\AutoSoftCommitVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\CommitWithinVisitor;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Visitor\UpdateHandler\UpdateLogVisitor;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Yaml;

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
        $client->expects(self::exactly(2))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(
                $this->getSchemaResult(),
                $this->getConfigResult()
            )
        ;

        (new ConfigGenerator($this->getMockedHandlers(1, 1, 1, 1, 1, 1, 1, 1, 1), $this->getDumpers(), __DIR__, $client))
            ->withExtension(DumperInterface::EXTENSION_YAML)
            ->withCore('foo')
            ->generate()
        ;

        // cleanup
        unlink(__DIR__.'/solrphp_solarium.yaml');
    }

    /**
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function testGeneratedFormat(): void
    {
        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $client->expects(self::exactly(4))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(
                $this->getSchemaResult(),
                $this->getConfigResult(),
                $this->getSchemaResult(),
                $this->getConfigResult()
            )
        ;

        (new ConfigGenerator($this->getHandlers(), $this->getDumpers(), __DIR__, $client))
            ->withExtension(DumperInterface::EXTENSION_YAML)
            ->withCore('foo')
            ->withBeautify(false)
            ->generate()
        ;

        $raw = Yaml::parse(file_get_contents(__DIR__.'/solrphp_solarium.yaml'))['solrphp_solarium'];

        self::assertArrayHasKey('managed_schemas', $raw);
        self::assertArrayHasKey('fields', $raw['managed_schemas']);
        self::assertCount(3, $raw['managed_schemas']['fields']);
        self::assertArrayHasKey('dynamic_fields', $raw['managed_schemas']);
        self::assertCount(3, $raw['managed_schemas']['dynamic_fields']);
        self::assertArrayHasKey('copy_fields', $raw['managed_schemas']);
        self::assertCount(3, $raw['managed_schemas']['copy_fields']);
        self::assertArrayHasKey('field_types', $raw['managed_schemas']);

        self::assertArrayHasKey('solr_configs', $raw);
        self::assertArrayHasKey('update_handler', $raw['solr_configs']);
        self::assertArrayHasKey('auto_commit', $raw['solr_configs']['update_handler']);
        self::assertArrayHasKey('max_time', $raw['solr_configs']['update_handler']['auto_commit']);
        self::assertArrayHasKey('query', $raw['solr_configs']);
        self::assertArrayHasKey('request_dispatcher', $raw['solr_configs']);
        self::assertArrayHasKey('request_handlers', $raw['solr_configs']);
        self::assertArrayHasKey('search_components', $raw['solr_configs']);

        (new ConfigGenerator($this->getHandlers(), $this->getDumpers(), __DIR__, $client))
            ->withExtension(DumperInterface::EXTENSION_YAML)
            ->withCore('foo')
            ->generate()
        ;
        $beautified = Yaml::parse(file_get_contents(__DIR__.'/solrphp_solarium.yaml'))['solrphp_solarium'];

        self::assertNotSame($raw, $beautified);

        // cleanup
        unlink(__DIR__.'/solrphp_solarium.yaml');
    }

    /**
     * @throws \Solrphp\SolariumBundle\ConfigGenerator\Exception\GeneratorException
     */
    public function testTypesGenerate(): void
    {
        $optionsOne = [
            'version' => 'v1',
            'method' => 'GET',
            'resultclass' => 'Solarium\Core\Query\Result\QueryType',
            'handler' => 'foo/schema',
        ];

        $paramsOne = [
            'wt' => 'schema.xml',
        ];

        $optionsTwo = [
            'version' => 'v1',
            'method' => 'GET',
            'resultclass' => 'Solarium\Core\Query\Result\QueryType',
            'handler' => 'foo/admin/file',
        ];

        $paramsTwo = [
            'wt' => 'xml',
            'file' => 'solrconfig.xml',
        ];

        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([new Curl(), new EventDispatcher()])
            ->onlyMethods(['execute'])
            ->getMock()
        ;

        $client->expects(self::exactly(2))
            ->method('execute')
            ->withConsecutive(
                [
                    self::callback(static function ($query) use ($optionsOne, $paramsOne) {
                        return $optionsOne === $query->getOptions() && $paramsOne === $query->getParams();
                    }),
                    self::callback(static function ($core) {
                        return 'foo' === $core;
                    }),
                ],
                [
                    self::callback(static function ($query) use ($optionsTwo, $paramsTwo) {
                        return $optionsTwo === $query->getOptions() && $paramsTwo === $query->getParams();
                    }),
                    self::callback(static function ($core) {
                        return 'foo' === $core;
                    }),
                ]
            )
            ->willReturn($this->getSchemaResult())
        ;

        (new ConfigGenerator($this->getMockedHandlers(1, 0, 1, 0, 0, 0, 0, 0, 0), $this->getDumpers(), __DIR__, $client))
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
    public function getSchemaResult(): Result
    {
        return new Result(new Query(), new Response($this->getSchemaXml(), ['HTTP 200 OK']));
    }

    /**
     * @return \Solarium\Core\Query\Result\Result
     *
     * @throws \Solarium\Exception\HttpException
     */
    public function getConfigResult(): Result
    {
        return new Result(new Query(), new Response($this->getConfigXml(), ['HTTP 200 OK']));
    }

    /**
     * @return array
     */
    private function getHandlers(): array
    {
        return [
            new CopyFieldGeneratorHandler(),
            new DynamicFieldGeneratorHandler(),
            new FieldGeneratorHandler(),
            new FieldTypeGeneratorHandler(),
            new QueryGeneratorHandler(),
            new RequestDispatcherGeneratorHandler(),
            new RequestHandlerGeneratorHandler(),
            new SearchComponentGeneratorHandler(),
            new UpdateHandlerGeneratorHandler(),
        ];
    }

    /**
     * @param int $copies
     * @param int $fields
     * @param int $dynamics
     * @param int $types
     * @param int $query
     * @param int $dispatcher
     * @param int $handler
     * @param int $component
     * @param int $update
     *
     * @return array
     *
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    private function getMockedHandlers(int $copies, int $fields, int $dynamics, int $types, int $query, int $dispatcher, int $handler, int $component, int $update): array
    {
        $copyFieldGenerator = $this->getMockBuilder(CopyFieldGeneratorHandler::class)->onlyMethods(['handle'])->getMock();
        $copyFieldGenerator->expects(self::exactly($copies))->method('handle');

        $fieldGenerator = $this->getMockBuilder(FieldGeneratorHandler::class)->onlyMethods(['handle'])->getMock();
        $fieldGenerator->expects(self::exactly($fields))->method('handle');

        $dynamicFieldGenerator = $this->getMockBuilder(DynamicFieldGeneratorHandler::class)->onlyMethods(['handle'])->getMock();
        $dynamicFieldGenerator->expects(self::exactly($dynamics))->method('handle');

        $fieldTypeGenerator = $this->getMockBuilder(FieldTypeGeneratorHandler::class)->setConstructorArgs([$this->getVisitors()])->onlyMethods(['handle'])->getMock();
        $fieldTypeGenerator->expects(self::exactly($types))->method('handle');

        $queryGenerator = $this->getMockBuilder(QueryGeneratorHandler::class)->setConstructorArgs([$this->getVisitors()])->onlyMethods(['handle'])->getMock();
        $queryGenerator->expects(self::exactly($query))->method('handle');

        $requestDispatcher = $this->getMockBuilder(RequestDispatcherGeneratorHandler::class)->setConstructorArgs([$this->getVisitors()])->onlyMethods(['handle'])->getMock();
        $requestDispatcher->expects(self::exactly($dispatcher))->method('handle');

        $requestHandler = $this->getMockBuilder(RequestHandlerGeneratorHandler::class)->setConstructorArgs([$this->getVisitors()])->onlyMethods(['handle'])->getMock();
        $requestHandler->expects(self::exactly($handler))->method('handle');

        $searchComponent = $this->getMockBuilder(SearchComponentGeneratorHandler::class)->onlyMethods(['handle'])->getMock();
        $searchComponent->expects(self::exactly($component))->method('handle');

        $updateHandler = $this->getMockBuilder(UpdateHandlerGeneratorHandler::class)->setConstructorArgs([$this->getVisitors()])->onlyMethods(['handle'])->getMock();
        $updateHandler->expects(self::exactly($update))->method('handle');

        return [
            $copyFieldGenerator,
            $fieldGenerator,
            $dynamicFieldGenerator,
            $fieldTypeGenerator,
            $queryGenerator,
            $requestDispatcher,
            $requestHandler,
            $searchComponent,
            $updateHandler,
        ];
    }

    /**
     * @return array<\Solrphp\SolariumBundle\Contract\ConfigGenerator\ConfigGeneratorVisitorInterface>
     */
    private function getVisitors(): array
    {
        return [
            new CharFilterFieldTypeVisitor(),
            new FilterFieldTypeVisitor(),
            new TokenizerFieldTypeVisitor(),
            new ResultCacheVisitor(),
            new DocumentCacheVisitor(),
            new FilterCacheVisitor(),
            new FieldValueCacheVisitor(),
            new RequestParserVisitor(),
            new AppendsVisitor(),
            new ComponentsVisitor(),
            new DefaultsVisitor(),
            new InvariantsVisitor(),
            new AutoCommitVisitor(),
            new AutoSoftCommitVisitor(),
            new CommitWithinVisitor(),
            new UpdateLogVisitor(),
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

    /**
     * @return string
     */
    private function getConfigXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" ?>
<config>
  <luceneMatchVersion>8.9.0</luceneMatchVersion>
  <dataDir>${solr.data.dir:}</dataDir>
  <directoryFactory name="DirectoryFactory"
                    class="${solr.directoryFactory:solr.NRTCachingDirectoryFactory}"/>
  <codecFactory class="solr.SchemaCodecFactory"/>
  <indexConfig>
    <lockType>${solr.lock.type:native}</lockType>
  </indexConfig>
  <updateHandler class="solr.DirectUpdateHandler2">
    <updateLog>
      <str name="dir">${solr.ulog.dir:}</str>
      <int name="numVersionBuckets">${solr.ulog.numVersionBuckets:65536}</int>
    </updateLog>
    <autoCommit>
      <maxTime>${solr.autoCommit.maxTime:15000}</maxTime>
      <openSearcher>false</openSearcher>
    </autoCommit>
    <autoSoftCommit>
      <maxTime>${solr.autoSoftCommit.maxTime:-1}</maxTime>
    </autoSoftCommit>
  </updateHandler>

  <query>
    <maxBooleanClauses>${solr.max.booleanClauses:1024}</maxBooleanClauses>
    <filterCache size="512"
                 initialSize="512"
                 autowarmCount="0"/>
    <queryResultCache size="512"
                      initialSize="512"
                      autowarmCount="0"/>
    <documentCache size="512"
                   initialSize="512"
                   autowarmCount="0"/>
    <enableLazyFieldLoading>true</enableLazyFieldLoading>
    <queryResultWindowSize>20</queryResultWindowSize>
    <queryResultMaxDocsCached>200</queryResultMaxDocsCached>
    <listener event="newSearcher" class="solr.QuerySenderListener">
      <arr name="queries">
      </arr>
    </listener>
    <listener event="firstSearcher" class="solr.QuerySenderListener">
      <arr name="queries">
      </arr>
    </listener>
    <useColdSearcher>false</useColdSearcher>
  </query>

  <requestDispatcher>
    <httpCaching never304="true" />
  </requestDispatcher>

  <requestHandler name="/select" class="solr.SearchHandler">
    <lst name="defaults">
      <str name="echoParams">explicit</str>
      <int name="rows">10</int>
    </lst>
  </requestHandler>

  <requestHandler name="/query" class="solr.SearchHandler">
    <lst name="defaults">
      <str name="echoParams">explicit</str>
      <str name="wt">json</str>
      <str name="indent">true</str>
    </lst>
  </requestHandler>

  <searchComponent name="spellcheck" class="solr.SpellCheckComponent">
    <lst name="spellchecker">
      <str name="name">default</str>
      <str name="field">_text_</str>
      <str name="classname">solr.DirectSolrSpellChecker</str>
    </lst>
  </searchComponent>

  <requestHandler name="/spell" class="solr.SearchHandler" startup="lazy">
    <lst name="defaults">
      <str name="spellcheck.dictionary">default</str>
      <str name="spellcheck">on</str>
      <str name="spellcheck.extendedResults">true</str>
      <str name="spellcheck.count">10</str>
      <str name="spellcheck.alternativeTermCount">5</str>
      <str name="spellcheck.maxResultsForSuggest">5</str>
      <str name="spellcheck.collate">true</str>
      <str name="spellcheck.collateExtendedResults">true</str>
      <str name="spellcheck.maxCollationTries">10</str>
      <str name="spellcheck.maxCollations">5</str>
    </lst>
    <arr name="last-components">
      <str>spellcheck</str>
    </arr>
  </requestHandler>
  <searchComponent name="terms" class="solr.TermsComponent"/>
</config>
XML;
    }
}
