<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Util;

use const PHP_EOL;
use Solarium\Exception\HttpException;
use Solrphp\SolariumBundle\Common\Response\RawSolrApiResponse;
use Solrphp\SolariumBundle\Common\Serializer\SolrSerializer;
use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Solrphp\SolariumBundle\Exception\SolrphpException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Error Util.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ErrorUtil
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param \Solrphp\SolariumBundle\Exception\SolrphpException $exception
     * @param int                                                $verbosity
     *
     * @return string
     *
     * @throws \JMS\Serializer\Exception\RuntimeException
     */
    public static function fromSolrphpException(SolrphpException $exception, int $verbosity): string
    {
        if (null === $previous = $exception->getPrevious()) {
            return '';
        }

        $content = ($previous instanceof HttpException) ? $previous->getBody() ?? '{}' : $previous->getMessage();

        $response = (new SolrSerializer())
            ->deserialize($content, RawSolrApiResponse::class, 'json');

        return self::fromResponse($response, $verbosity);
    }

    /**
     * @param \Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface $response
     * @param int                                                                 $verbosity
     *
     * @return string
     */
    public static function fromResponse(ResponseInterface $response, int $verbosity): string
    {
        $return = (($error = $response->getError()) && ($message = $error->getMessage())) ? $message : '[unable to get error message]';
        $return .= sprintf(' (%d)', $response->getResponseHeader()->getStatus());

        if ($verbosity >= OutputInterface::VERBOSITY_VERBOSE && $error) {
            $return .= self::parseDetails($error->getDetails());
        }

        if ($verbosity >= OutputInterface::VERBOSITY_VERY_VERBOSE && $error) {
            $return .= self::parseMetadata($error->getMetaData());
        }

        if ($verbosity >= OutputInterface::VERBOSITY_DEBUG && $error) {
            $return .= self::parseStacktrace($error->getTrace());
        }

        return trim($return);
    }

    /**
     * @param string|null $stacktrace
     *
     * @return string
     */
    private static function parseStacktrace(?string $stacktrace): string
    {
        if (null === $stacktrace) {
            return '';
        }

        $message = <<<'MESSAGE'


stacktrace:

MESSAGE;

        return $message.$stacktrace;
    }

    /**
     * @param array<int, array<string, array<string>>> $details
     *
     * @return string
     */
    private static function parseDetails(array $details): string
    {
        if (0 === \count($details)) {
            return '';
        }

        $message = <<<'MESSAGE'


details:
MESSAGE;

        foreach ($details as $key => $detail) {
            if (false === isset($detail['errorMessages'])) {
                continue;
            }

            $message .= PHP_EOL.sprintf(' #%d: %s', $key, implode(PHP_EOL, $detail['errorMessages']));
        }

        return $message;
    }

    /**
     * @param array<string, string> $metadata
     *
     * @return string
     */
    private static function parseMetadata(array $metadata): string
    {
        if (0 === \count($metadata)) {
            return '';
        }

        $message = <<<'MESSAGE'


metadata:
MESSAGE;

        $i = 0;

        do {
            $message .= PHP_EOL.sprintf(' #%d %s: %s', $i, array_shift($metadata), array_shift($metadata));
            ++$i;
        } while (\count($metadata));

        return $message;
    }
}
