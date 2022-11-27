<?php

declare(strict_types=1);

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use App\GlobalModule;

require __DIR__ . '/../vendor/autoload.php';

// Create our DI container
$container = new Container();
Container::$globalInstance = $container;
$container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);

// Build and run our application
$globalModule = new GlobalModule($container);
$globalModule->bootstrap();
$appBuilderClass = (string)\getenv('APP_BUILDER_API');

if (!\class_exists($appBuilderClass) || !\is_subclass_of($appBuilderClass, IApplicationBuilder::class)) {
    throw new TypeError('Environment variable "APP_BUILDER_API" must implement ' . IApplicationBuilder::class);
}

exit(
    $container->resolve($appBuilderClass)
        ->withModule($globalModule)
        ->build()
        ->run()
);
