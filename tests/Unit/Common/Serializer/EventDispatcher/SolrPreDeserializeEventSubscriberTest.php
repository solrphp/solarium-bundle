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
}
