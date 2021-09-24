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

use Solrphp\SolariumBundle\Contract\SolrApi\Response\ResponseInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Error Util.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ErrorUtil
{
    /**
     * not instantiable.
     */
    private function __construct()
    {
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

        if ($verbosity >= OutputInterface::VERBOSITY_VERBOSE && $error && \count($error->getMetaData())) {
            $meta = $error->getMetaData();
            $metaMessage = null;
            $i = 1;

            do {
                $metaMessage .= \PHP_EOL.sprintf(' #%d %s: %s', $i, array_shift($meta), array_shift($meta));
                ++$i;
            } while (\count($meta));

            $return .= $metaMessage;
        }

        return trim($return);
    }
}
