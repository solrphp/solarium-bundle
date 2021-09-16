<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Exception;

use Throwable;

/**
 * Unexpected Value Exception.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class UnexpectedValueException extends \RuntimeException
{
    /**
     * @param string          $message
     * @param \Throwable|null $previous
     */
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
