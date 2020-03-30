<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Application\BootstrapperCollection;
use Aphiria\Configuration\GlobalConfigurationBuilder;
use Aphiria\DependencyInjection\Binders\IBinderDispatcher;
use Aphiria\DependencyInjection\Binders\LazyBinderDispatcher;
use Aphiria\DependencyInjection\Binders\Metadata\Caching\FileBinderMetadataCollectionCache;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use Aphiria\Framework\Api\Builders\ApiApplicationBuilder;
use Aphiria\Framework\Configuration\Bootstrappers\ConfigurationBootstrapper;
use Aphiria\Framework\Configuration\Bootstrappers\EnvironmentVariableBootstrapper;
use Aphiria\Framework\Exceptions\Bootstrappers\GlobalExceptionHandlerBootstrapper;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\StreamResponseWriter;
use App\App;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ----------------------------------------------------------
 * Bootstrap the application
 * ----------------------------------------------------------
 */
$container = new Container();
$container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);
Container::$globalInstance = $container;
$globalConfigurationBuilder = (new GlobalConfigurationBuilder)->withEnvironmentVariables()
    ->withPhpFileConfigurationSource(__DIR__ . '/../config.php');
(new BootstrapperCollection)->addMany([
    new EnvironmentVariableBootstrapper(__DIR__ . '/../.env'),
    new ConfigurationBootstrapper($globalConfigurationBuilder),
    new GlobalExceptionHandlerBootstrapper($container)
])->bootstrapAll();

/**
 * ----------------------------------------------------------
 * Set up the binder dispatcher
 * ----------------------------------------------------------
 */
$binderDispatcher = new LazyBinderDispatcher(
    getenv('APP_ENV') === 'production'
        ? new FileBinderMetadataCollectionCache(__DIR__ . '/../tmp/framework/binderMetadataCollectionCache.txt')
        : null
);
$container->bindInstance(IBinderDispatcher::class, $binderDispatcher);

/**
 * ----------------------------------------------------------
 * Build and run our application
 * ----------------------------------------------------------
 */
$appBuilder = new ApiApplicationBuilder($container);
(new App)->build($appBuilder);
$app = $appBuilder->build();
$response = $app->handle($container->resolve(IHttpRequestMessage::class));
(new StreamResponseWriter)->writeResponse($response);
