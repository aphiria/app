<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloader = require __DIR__ . '/vendor/autoload.php';

/** @link https://github.com/schmittjoh/serializer/issues/855 */
AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);
