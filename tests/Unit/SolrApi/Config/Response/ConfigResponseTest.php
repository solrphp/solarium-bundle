<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\SolrApi\Config\Response;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\SolrApi\Config\Config\SolrConfig;
use Solrphp\SolariumBundle\SolrApi\Config\Response\ConfigResponse;

/**
 * ConfigResponseTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigResponseTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testConfigResponseAccessors(): void
    {
        $response = new ConfigResponse();
        $config = new SolrConfig(new ArrayCollection(['foo']));

        $response->setConfig($config);

        self::assertSame($config, $response->getConfig());
    }
}
