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
use Solarium\Exception\HttpException;
use Solrphp\SolariumBundle\Common\Response\Error;
use Solrphp\SolariumBundle\Common\Response\Header;
use Solrphp\SolariumBundle\Common\Response\RawSolrApiResponse;
use Solrphp\SolariumBundle\Common\Util\ErrorUtil;
use Solrphp\SolariumBundle\Exception\ProcessorException;
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
        self::assertSame('lorem ipsum (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_VERBOSE));
        self::assertSame('lorem ipsum (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_VERY_VERBOSE));
        self::assertSame('lorem ipsum (2)', ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_DEBUG));
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
        $error->setDetails([
            ['errorMessages' => ['Error loading class']],
            ['foo' => ['bar']],
            ['errorMessages' => ['Error loading class']],
        ]);

        $response = new RawSolrApiResponse();
        $response->setResponseHeader($header);
        $response->setError($error);
        $expected = <<<'MESSAGE'
lorem ipsum (2)

details:
 #0: Error loading class
 #2: Error loading class
MESSAGE;

        self::assertSame($expected, ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_VERBOSE));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testMetadataVerbosityVeryVerbose(): void
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

metadata:
 #0 foo: bar
 #1 baz: qux
MESSAGE;

        self::assertSame($expected, ErrorUtil::fromResponse($response, OutputInterface::VERBOSITY_VERY_VERBOSE));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromHttpException(): void
    {
        $httpException = new HttpException('', 400, $this->getError());
        $processorException = new ProcessorException('foo', $httpException);

        self::assertSame($this->getNormal(), ErrorUtil::fromSolrphpException($processorException, OutputInterface::VERBOSITY_NORMAL));
        self::assertSame($this->getVerbose(), ErrorUtil::fromSolrphpException($processorException, OutputInterface::VERBOSITY_VERBOSE));
        self::assertSame($this->getVeryVerbose(), ErrorUtil::fromSolrphpException($processorException, OutputInterface::VERBOSITY_VERY_VERBOSE));
        self::assertSame($this->getDebug(), ErrorUtil::fromSolrphpException($processorException, OutputInterface::VERBOSITY_DEBUG));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromRuntimeException(): void
    {
        $runtimeException = new \RuntimeException($this->getHeaderAndSome('lorem ipsum'));
        $processorException = new ProcessorException('foo', $runtimeException);

        self::assertSame('[unable to get error message] (500)', ErrorUtil::fromSolrphpException($processorException, OutputInterface::VERBOSITY_NORMAL));
    }

    /**
     * @return string
     */
    private function getNormal(): string
    {
        return 'foo (500)';
    }

    /**
     * @return string
     */
    private function getDebug(): string
    {
        return <<<'MESSAGE'
foo (500)

details:
 #0: Error loading class
 #1: Error loading class

metadata:
 #0 foo: bar
 #1 baz: qux

stacktrace:
java.lang.RuntimeException: The JSON must be an Object
	at org.apache.solr.common.util.CommandOperation.parse(CommandOperation.java:282)
	at org.apache.solr.common.util.CommandOperation.readCommands(CommandOperation.java:362)
MESSAGE;
    }

    /**
     * @return string
     */
    private function getVeryVerbose(): string
    {
        return <<<'MESSAGE'
foo (500)

details:
 #0: Error loading class
 #1: Error loading class

metadata:
 #0 foo: bar
 #1 baz: qux
MESSAGE;
    }

    /**
     * @return string
     */
    private function getVerbose(): string
    {
        return <<<'MESSAGE'
foo (500)

details:
 #0: Error loading class
 #1: Error loading class
MESSAGE;
    }

    /**
     * @param string $some
     *
     * @return string
     */
    private function getHeaderAndSome(string $some): string
    {
        $message = <<<'MESSAGE'
{
    "responseHeader":{
        "status":500,
        "QTime":0
    },
    "body": "%s"
}

MESSAGE;

        return sprintf($message, $some);
    }

    /**
     * @return string
     */
    private function getError(): string
    {
        return <<<'JSON'
{
"responseHeader":{
    "status":500,
    "QTime":0
},
"error": {
    "msg":"foo",
    "metadata":[
      "foo","bar",
      "baz","qux"
    ],
    "details":[{
        "foo":{
          "name":"bar"
        },
        "errorMessages":[
            "Error loading class"
        ]
      },
      {
        "foo":{
          "name":"bar"
        },
        "errorMessages":[
            "Error loading class"
        ]
      }
    ],
    "trace": "java.lang.RuntimeException: The JSON must be an Object\n\tat org.apache.solr.common.util.CommandOperation.parse(CommandOperation.java:282)\n\tat org.apache.solr.common.util.CommandOperation.readCommands(CommandOperation.java:362)"
}
 }
JSON;
    }
}
