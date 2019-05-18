<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Api\ApiKernel;
use Aphiria\Api\ContainerDependencyResolver;
use Aphiria\Api\Controllers\IRouteActionInvoker;
use Aphiria\Api\Exceptions\IExceptionHandler;
use Aphiria\Api\IDependencyResolver;
use Aphiria\Configuration\ApplicationBuilder;
use Aphiria\Middleware\MiddlewarePipelineFactory;
use Aphiria\Net\Http\ContentNegotiation\IContentNegotiator;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;
use Aphiria\Routing\Matchers\IRouteMatcher;
use App\Application\ApplicationConfiguration;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Inspection\BindingInspectorBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\Caching\FileBootstrapperBindingCache;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ----------------------------------------------------------
 * Load environment config files
 * ----------------------------------------------------------
 *
 * Note:  For performance in production, it's highly suggested
 * you set environment variables on the server itself
 */
require __DIR__ . '/../.env.app.php';

/**
 * ----------------------------------------------------------
 * Set up the DI container
 * ----------------------------------------------------------
 */
$container = new Container();
$container->bindInstance([IContainer::class, Container::class], $container);

/**
 * ----------------------------------------------------------
 * Build our application
 * ----------------------------------------------------------
 */
$bootstrapperDispatcher = new BindingInspectorBootstrapperDispatcher(
    $container,
    Environment::getVar('ENV_NAME') === Environment::PRODUCTION
        ? new FileBootstrapperBindingCache(__DIR__ . '/../tmp/framework/bootstrapperInspections.txt')
        : null
);
$appBuilder = new ApplicationBuilder($container, $bootstrapperDispatcher);
ApplicationConfiguration::configure($appBuilder);
$appBuilder->build();

/**
 * ----------------------------------------------------------
 * Handle the request
 * ----------------------------------------------------------
 */
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$container->bindInstance(IHttpRequestMessage::class, $request);
$container->resolve(IExceptionHandler::class)->setRequest($request);
$apiKernel = new ApiKernel(
    $container->resolve(IRouteMatcher::class),
    $container->hasBinding(IDependencyResolver::class)
        ? $container->resolve(IDependencyResolver::class)
        : new ContainerDependencyResolver($container),
    $container->resolve(IContentNegotiator::class),
    $container->hasBinding(MiddlewarePipelineFactory::class)
        ? $container->resolve(MiddlewarePipelineFactory::class)
        : null,
    $container->hasBinding(IRouteActionInvoker::class)
        ? $container->resolve(IRouteActionInvoker::class)
        : null
);
$response = $apiKernel->handle($request);
(new StreamResponseWriter)->writeResponse($response);
