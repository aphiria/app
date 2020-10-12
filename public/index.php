<?php

declare(strict_types=1);

use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use Aphiria\Framework\Api\Builders\ApiApplicationBuilder;
use Aphiria\Net\Http\IRequest;
use Aphiria\Net\Http\StreamResponseWriter;
use App\App;

require __DIR__ . '/../vendor/autoload.php';

// Create our DI container
$container = new Container();
Container::$globalInstance = $container;
$container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);

// Build and run our application
$response = (new ApiApplicationBuilder($container))->withModule(new App($container))
    ->build()
    ->handle($container->resolve(IRequest::class));
(new StreamResponseWriter())->writeResponse($response);
