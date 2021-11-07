<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Tests\Integration;

use Solrphp\SolariumBundle\SolrphpSolariumBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Test Kernel.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class TestKernel extends Kernel
{
    public const ENVIRONMENT_STANDALONE = 'standalone';
    public const ENVIRONMENT_CLOUD = 'cloud';

    private string $tempDir;

    /**
     * @param string $environment
     * @param bool   $debug
     */
    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->tempDir = sys_get_temp_dir().'/solrphp-solarium';
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SolrphpSolariumBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $directory = self::ENVIRONMENT_STANDALONE === $this->environment ? 'StandAlone' : 'Cloud';

        $loader->load(sprintf('%s/%s/config/config.yaml', __DIR__, $directory));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        return $this->tempDir.'/cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return $this->tempDir.'/logs';
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectDir(): string
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerClass(): string
    {
        return parent::getContainerClass().sha1(__NAMESPACE__);
    }
}
