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
use Aphiria\DependencyInjection\Binders\Inspection\BindingInspectionContainer;
use Aphiria\DependencyInjection\Binders\Inspection\BindingInspector;
use Aphiria\DependencyInjection\Binders\Inspection\BindingInspectorBinderDispatcher;
use Aphiria\DependencyInjection\Binders\Inspection\Caching\FileBinderBindingCache;
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
$binderDispatcher = new BindingInspectorBinderDispatcher(
    $container,
    getenv('APP_ENV') === 'production'
        ? new FileBinderBindingCache(__DIR__ . '/../tmp/framework/binderInspections.txt')
        : null,
    new BindingInspector(new BindingInspectionContainer($container))
);
$container->bindInstance(IBinderDispatcher::class, $binderDispatcher);

/**
 * ----------------------------------------------------------
 * Build and run our application
 * ----------------------------------------------------------
 */
$appBuilder = new ApiApplicationBuilder($container);
(new App($appBuilder))->configure();
$app = $appBuilder->build();
$response = $app->handle($container->resolve(IHttpRequestMessage::class));
(new StreamResponseWriter)->writeResponse($response);
