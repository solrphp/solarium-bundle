<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\ConfigGenerator\Handler\Param;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\ParamConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Generator\SchemaConfigurationGenerator;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\ParameterSetMapGeneratorHandler;
use Solrphp\SolariumBundle\ConfigGenerator\Handler\Param\visitor\ParametersVisitor;
use Symfony\Component\DomCrawler\Crawler;

/**
 * ParameterSetMap Generator Handler Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ParameterSetMapGeneratorHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testHandle(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getParamXml());

        $nodes = (new ParameterSetMapGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(2, $nodes);
        self::assertArrayHasKey('name', $nodes[0]);
        self::assertSame('henkelmans', $nodes[0]['name']);
        self::assertArrayHasKey('parameters', $nodes[0]);
        self::assertCount(2, $nodes[0]['parameters']);
        self::assertArrayHasKey('name', $nodes[0]['parameters'][0]);
        self::assertArrayHasKey('value', $nodes[0]['parameters'][0]);
        self::assertSame('facet', $nodes[0]['parameters'][0]['name']);
        self::assertSame('true', $nodes[0]['parameters'][0]['value']);
        self::assertArrayHasKey('_invariants_', $nodes[0]);
        self::assertCount(2, $nodes[0]['_invariants_']);
        self::assertArrayHasKey('name', $nodes[0]['_invariants_'][1]);
        self::assertArrayHasKey('value', $nodes[0]['_invariants_'][1]);
        self::assertSame('foo', $nodes[0]['_invariants_'][1]['name']);
        self::assertSame('bar', $nodes[0]['_invariants_'][1]['value']);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testHandleNoParameters(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getParamNoParamXml());

        $nodes = (new ParameterSetMapGeneratorHandler())->handle($crawler, $closure);

        self::assertCount(1, $nodes);
        self::assertArrayNotHasKey('parameters', $nodes[0]);
        self::assertArrayHasKey('_invariants_', $nodes[0]);
    }

    /**
     * test visitor assignment.
     */
    public function testVisitorAssignment(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getParamXml());

        (new ParameterSetMapGeneratorHandler($this->getMockedVisitors()))->handle($crawler, $closure);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSupports(): void
    {
        self::assertTrue((new ParameterSetMapGeneratorHandler())->supports(ParamConfigurationGenerator::TYPE_PARAMETER_SET_MAP));
        self::assertFalse((new ParameterSetMapGeneratorHandler())->supports(SchemaConfigurationGenerator::TYPE_FIELD_TYPE));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testArrayCombineFailSave(): void
    {
        $closure = \Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']);
        $crawler = new Crawler($this->getParamXml());

        $nodes = (new ParameterSetMapGeneratorHandler())->handle($crawler, $closure);

        self::assertArrayNotHasKey('foo', $nodes[0]);
    }

    /**
     * @return string
     */
    public function getParamXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>

<lst name="responseHeader">
  <int name="status">0</int>
  <int name="QTime">0</int>
</lst>
<lst name="response">
  <int name="znodeVersion">0</int>
  <lst name="params">
    <lst name="henkelmans">
      <str name="facet">true</str>
      <long name="facet.limit">5</long>
      <lst name="_invariants_">
        <bool name="facet">true</bool>
        <str name="foo">bar</str>
      </lst>
      <lst name="foo">
        <long name="v">3</long>
      </lst>
    </lst>
    <lst name="bar">
      <str name="facet">true</str>
      <long name="facet.limit">5</long>
      <lst name="_invariants_">
        <bool name="facet">true</bool>
        <str name="foo">bar</str>
      </lst>
      <lst name="foo">
        <long name="v">3</long>
      </lst>
    </lst>
  </lst>
</lst>
</response>
XML;
    }

    /**
     * @return string
     */
    public function getParamNoParamXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>

<lst name="responseHeader">
  <int name="status">0</int>
  <int name="QTime">0</int>
</lst>
<lst name="response">
  <int name="znodeVersion">0</int>
  <lst name="params">
    <lst name="henkelmans">
      <lst name="_invariants_">
        <bool name="facet">true</bool>
        <str name="foo">bar</str>
      </lst>
      <lst name="foo">
        <long name="v">3</long>
      </lst>
    </lst>
  </lst>
</lst>
</response>
XML;
    }

    /**
     * @return array
     *
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws \PHPUnit\Framework\MockObject\ClassAlreadyExistsException
     * @throws \PHPUnit\Framework\MockObject\ClassIsFinalException
     * @throws \PHPUnit\Framework\MockObject\DuplicateMethodException
     * @throws \PHPUnit\Framework\MockObject\InvalidMethodNameException
     * @throws \PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException
     * @throws \PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException
     * @throws \PHPUnit\Framework\MockObject\OriginalConstructorInvocationRequiredException
     * @throws \PHPUnit\Framework\MockObject\ReflectionException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \PHPUnit\Framework\MockObject\UnknownTypeException
     */
    private function getMockedVisitors(): array
    {
        $mock = $this->getMockBuilder(ParametersVisitor::class)->getMock();
        $mock->expects(self::exactly(2))->method('visit');

        return [
            $mock,
        ];
    }
}
