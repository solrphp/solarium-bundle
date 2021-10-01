<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Serializer\EventDispatcher;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Serializer\Data\PrepareCallable;
use Solrphp\SolariumBundle\Common\Serializer\EventDispatcher\SolrPreDeserializeEventSubscriber;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\SolrApi\Param\Response\ParamResponse;

/**
 * SolrPreDeserializeEventSubscriberTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrPreDeserializeEventSubscriberTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSubscribedEvents(): void
    {
        $subs = SolrPreDeserializeEventSubscriber::getSubscribedEvents();

        self::assertSame('serializer.pre_deserialize', $subs[0]['event']);
        self::assertSame('json', $subs[0]['format']);
        self::assertSame(ResponseInterface::class, $subs[0]['class']);

        self::assertSame('serializer.pre_deserialize', $subs[1]['event']);
        self::assertSame('json', $subs[1]['format']);
        self::assertSame(ResponseInterface::class, $subs[1]['class']);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPreDeserialize(): void
    {
        $context = DeserializationContext::create()
            ->setAttribute('solrphp.real_class', 'Foo\Bar');

        $data = ['key-one' => 'one', 'key-two' => ['Sub-one' => 'one']];
        $type = ['name' => ResponseInterface::class, 'params' => ['foo' => 'bar']];

        $event = new PreDeserializeEvent($context, $data, $type);

        $subscriber = new SolrPreDeserializeEventSubscriber(\Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']));

        $subscriber->onPreDeserialize($event);

        $processedData = $event->getData();
        $processedType = $event->getType();

        self::assertArrayNotHasKey('key-one', $processedData);
        self::assertArrayHasKey('key_one', $processedData);
        self::assertArrayNotHasKey('key-two', $processedData);
        self::assertArrayHasKey('key_two', $processedData);
        self::assertArrayNotHasKey('Sub-one', $processedData['key_two']);
        self::assertArrayHasKey('sub_one', $processedData['key_two']);

        self::assertSame('Foo\Bar', $processedType['name']);
        self::assertSame(['foo' => 'bar'], $processedType['params']);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testPreDeserializeParams(): void
    {
        $context = DeserializationContext::create()
            ->setAttribute('solrphp.real_class', ParamResponse::class);

        $type = ['name' => ResponseInterface::class, 'params' => ['foo' => 'bar']];

        $event = new PreDeserializeEvent($context, $this->getData(), $type);
        $subscriber = new SolrPreDeserializeEventSubscriber(\Closure::fromCallable([new PrepareCallable(), 'prepareSolrResponse']));
        $subscriber->normalizeParamsResponse($event);

        $processedData = $event->getData();
        $processedType = $event->getType();

        self::assertSame(ResponseInterface::class, $processedType['name']);
        self::assertSame(['foo' => 'bar'], $processedType['params']);

        self::assertArrayHasKey('params', $processedData);
        self::assertArrayHasKey('parameters', $processedData['params'][1]);
        self::assertArrayHasKey('facet', $processedData['params'][1]['parameters']);
        self::assertSame('frits', $processedData['params'][1]['name']);
        self::assertArrayHasKey('_invariants_', $processedData['params'][0]);
        self::assertArrayNotHasKey('_appends_', $processedData['params'][0]);
        self::assertArrayNotHasKey('facet', $processedData['params'][0]);
        self::assertArrayNotHasKey('', $processedData['params'][1]['parameters']);
        self::assertArrayNotHasKey('_invariants_', $processedData['params'][1]);
        self::assertArrayHasKey('_appends_', $processedData['params'][1]);

        self::assertSame($this->getArrayResult(), $processedData['params']);
    }

    /**
     * @return array[]
     */
    private function getArrayResult(): array
    {
        return [
            [
                '_invariants_' => [
                    'facet' => '1',
                ],
                'parameters' => [
                    'facet' => 'true',
                    'facet.limit' => '5',
                ],
                'name' => 'henkelmans',
            ],
            [
                '_appends_' => [
                    'facet' => '1',
                ],
                'parameters' => [
                    'facet' => 'true',
                    'facet.limit' => '5',
                ],
                'name' => 'frits',
            ], ];
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return json_decode('{
  "responseHeader":{
    "status":0,
    "QTime":0},
  "response":{
    "znodeVersion":0,
    "params":{
      "henkelmans":{
        "facet":"true",
        "facet.limit":"5",
        "_invariants_":{"facet":"1"},
        "":{"v":2}},
      "frits":{
        "facet":"true",
        "facet.limit":"5",
        "_appends_":{"facet":"1"},
        "":{"v":1}}}}}', true);
    }
}
