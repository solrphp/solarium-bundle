<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\ConfigGenerator\Dumper;

use Laminas\Code\Generator\FileGenerator;
use Solrphp\SolariumBundle\Contract\ConfigGenerator\DumperInterface;

/**
 * Php Dumper.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PhpDumper implements DumperInterface
{
    /**
     * {@inheritdoc}
     */
    public function dump(array $config, string $rootNode, array $types): string
    {
        $content = preg_replace(['/[0-9]+ => /', '/(^array \(|\n[\s]+array \()/', '/\)/', '/([\s]+)\],\[/'], ['', '[', ']', '$1],$1['], var_export($config, true));

        $file = FileGenerator::fromArray([
            'body' => sprintf('$container->loadFromExtension(\'%s\', %s);', $rootNode, $content),
        ]);

        return $file->generate();
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtension(): string
    {
        return DumperInterface::EXTENSION_PHP;
    }
}
