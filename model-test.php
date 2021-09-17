<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;
use Solrphp\SolariumBundle\Tests\Generator\ModelTestGenerator;
use Symfony\Component\Finder\Finder;

$autoloader = require __DIR__.'/vendor/autoload.php';

AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

$finder = new Finder();

foreach ($finder->in(__DIR__.'/src/SolrApi/*/Model')->notName('*Trait.php')->name('*.php')->files() as $file) {
    (new ModelTestGenerator($file->getFilenameWithoutExtension()))->generate();
}
