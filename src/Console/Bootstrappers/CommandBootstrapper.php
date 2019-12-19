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

use Aphiria\Console\Commands\AggregateCommandRegistrant;
use Aphiria\Console\Commands\Annotations\AnnotationCommandRegistrant;
use Aphiria\Console\Commands\Caching\CachedCommandRegistrant;
use Aphiria\Console\Commands\Caching\FileCommandRegistryCache;
use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\Console\Commands\ContainerCommandHandlerResolver;
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
        $commands = new CommandRegistry();
        $container->bindInstance(CommandRegistry::class, $commands);

        if (getenv('APP_ENV') === 'production') {
            $commandCache = new FileCommandRegistryCache(__DIR__ . '/../../../tmp/framework/console/commandCache.txt');
            $commandRegistrant = new CachedCommandRegistrant($commandCache);
            $container->bindInstance([AggregateCommandRegistrant::class, CachedCommandRegistrant::class], $commandRegistrant);
        } else {
            $commandRegistrant = new AggregateCommandRegistrant();
            $container->bindInstance(AggregateCommandRegistrant::class, $commandRegistrant);
        }

        // Register some command annotation dependencies
        $commandAnnotationRegistrant = new AnnotationCommandRegistrant(
            __DIR__ . '/../..',
            new ContainerCommandHandlerResolver($container)
        );
        $container->bindInstance(AnnotationCommandRegistrant::class, $commandAnnotationRegistrant);
    }
}
