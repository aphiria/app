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
use Aphiria\DependencyInjection\Binders\Inspection\BindingInspectorBinderDispatcher;
use Aphiria\DependencyInjection\Binders\Inspection\Caching\FileBinderBindingCache;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use Aphiria\Framework\Api\Builders\ApiApplicationBuilder;
use Aphiria\Framework\Configuration\Bootstrappers\ConfigurationBootstrapper;
use Aphiria\Framework\Configuration\Bootstrappers\EnvironmentVariableBootstrapper;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;
use App\App;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ----------------------------------------------------------
 * Bootstrap the application
 * ----------------------------------------------------------
 */
$globalConfigurationBuilder = (new GlobalConfigurationBuilder)->withEnvironmentVariables()
    ->withPhpFileConfigurationSource(__DIR__ . '/../config.php');
(new BootstrapperCollection)->addMany([
    new EnvironmentVariableBootstrapper(__DIR__ . '/../.env'),
    new ConfigurationBootstrapper($globalConfigurationBuilder)
])->bootstrapAll();

/**
 * ----------------------------------------------------------
 * Set up the DI container
 * ----------------------------------------------------------
 */
$container = new Container();
$container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);
Container::$globalInstance = $container;
$binderDispatcher = new BindingInspectorBinderDispatcher(
    $container,
    getenv('APP_ENV') === 'production'
        ? new FileBinderBindingCache(__DIR__ . '/../tmp/framework/binderInspections.txt')
        : null
);
$container->bindInstance(IBinderDispatcher::class, $binderDispatcher);

/**
 * ----------------------------------------------------------
 * Build and run our application
 * ----------------------------------------------------------
 */
$appBuilder = new ApiApplicationBuilder($container);
(new App($appBuilder, $container))->configure();
$app = $appBuilder->build();
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$container->bindInstance(IHttpRequestMessage::class, $request);
$response = $app->handle($request);
(new StreamResponseWriter)->writeResponse($response);
