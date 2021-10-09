<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Stub\ConfigGenerator;

use Solrphp\SolariumBundle\ConfigGenerator\Contract\DumperInterface;

/**
 * Stub Dumper.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class StubDumper implements DumperInterface
{
    /**
     * {@inheritDoc}
     */
    public function dump(array $config, string $rootNode, array $types, bool $beautify = true): string
    {
        return 'foo';
    }

    /**
     * {@inheritDoc}
     */
    public static function getExtension(): string
    {
        return 'yaml';
    }
}
