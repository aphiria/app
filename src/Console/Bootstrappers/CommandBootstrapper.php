<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Console\Bootstrappers;

use Aphiria\Console\Commands\Caching\FileCommandRegistryCache;
use Aphiria\Console\Commands\ICommandRegistryFactory;
use Aphiria\Console\Commands\LazyCommandRegistryFactory;
use Aphiria\ConsoleCommandAnnotations\ContainerCommandHandlerResolver;
use Aphiria\ConsoleCommandAnnotations\ICommandAnnotationRegistrant;
use Aphiria\ConsoleCommandAnnotations\ReflectionCommandAnnotationRegistrant;
use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;

/**
 * Defines the console command bootstrapper
 */
final class CommandBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        if (getenv('APP_ENV') === 'production') {
            $commandCache = new FileCommandRegistryCache(__DIR__ . '/../../../tmp/framework/console/commandCache.txt');
        } else {
            $commandCache = null;
        }

        $commandFactory = new LazyCommandRegistryFactory(null, $commandCache);
        $container->bindInstance([ICommandRegistryFactory::class, LazyCommandRegistryFactory::class], $commandFactory);
        // Register some command annotation dependencies
        $commandAnnotationRegistrant = new ReflectionCommandAnnotationRegistrant(
            __DIR__ . '/../..',
            new ContainerCommandHandlerResolver($container)
        );
        $container->bindInstance(ICommandAnnotationRegistrant::class, $commandAnnotationRegistrant);
    }
}
