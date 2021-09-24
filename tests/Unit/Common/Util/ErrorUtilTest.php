<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Unit\Common\Util;

use PHPUnit\Framework\TestCase;
use Solrphp\SolariumBundle\Common\Response\Error;
use Solrphp\SolariumBundle\Common\Response\Header;
use Solrphp\SolariumBundle\Common\Response\RawSolrApiResponse;
use Solrphp\SolariumBundle\Common\Util\ErrorUtil;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ErrorUtil Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ErrorUtilTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNoError(): void
    {
        $header = new Header();
        $header->setStatus(2);

        $response = new RawSolrApiResponse();
        $response->setResponseHeader($header);

        self::assertSame('[unable to get error message] (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_NORMAL));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNoErrorMessage(): void
    {
        $header = new Header();
        $header->setStatus(2);

        $error = new Error();

        $response = new RawSolrApiResponse();
        $response->setResponseHeader($header);
        $response->setError($error);

        self::assertSame('[unable to get error message] (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_NORMAL));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testErrorMessage(): void
    {
        $header = new Header();
        $header->setStatus(2);

        $error = new Error();
        $error->setMessage('lorem ipsum');

        $response = new RawSolrApiResponse();
        $response->setResponseHeader($header);
        $response->setError($error);

        self::assertSame('lorem ipsum (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_NORMAL));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testErrorMessageTrim(): void
    {
        $header = new Header();
        $header->setStatus(2);

        $error = new Error();
        $error->setMessage('        lorem ipsum');

        $response = new RawSolrApiResponse();
        $response->setResponseHeader($header);
        $response->setError($error);

        self::assertSame('lorem ipsum (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_NORMAL));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testMetadataVerbosityNormal(): void
    {
        $header = new Header();
        $header->setStatus(2);

        $error = new Error();
        $error->setMessage('lorem ipsum');
        $error->setMetadata([
            'foo', 'bar', 'baz', 'qux',
        ]);

        $response = new RawSolrApiResponse();
        $response->setResponseHeader($header);
        $response->setError($error);

        self::assertSame('lorem ipsum (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_NORMAL));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testMetadataVerbosityVerbose(): void
    {
        $header = new Header();
        $header->setStatus(2);

        $error = new Error();
        $error->setMessage('lorem ipsum');
        $error->setMetadata([
            'foo', 'bar', 'baz', 'qux',
        ]);

        $response = new RawSolrApiResponse();
        $response->setResponseHeader($header);
        $response->setError($error);
        $expected = <<<'MESSAGE'
lorem ipsum (2)
 #1 foo: bar
 #2 baz: qux
MESSAGE;

        self::assertSame($expected, ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_VERBOSE));
    }
}
