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
use Aphiria\Api\Exceptions\IExceptionHandler;
use Aphiria\Configuration\ApplicationBuilder;
use Aphiria\Net\Http\ContentNegotiation\IContentNegotiator;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;
use Aphiria\Routing\Matchers\IRouteMatcher;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ----------------------------------------------------------
 * Set up the DI container
 * ----------------------------------------------------------
 */
$container = new Container();
$container->bindInstance(IContainer::class, $container);

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
 * Build our application
 * ----------------------------------------------------------
 */
$appBuilder = new ApplicationBuilder($container);
(require __DIR__ . '/../config/http.php')($appBuilder);
$appBuilder->build();

/**
 * ----------------------------------------------------------
 * Handle the request
 * ----------------------------------------------------------
 */
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$container->resolve(IExceptionHandler::class)->setRequest($request);
$apiKernel = new ApiKernel(
    $container->resolve(IRouteMatcher::class),
    new ContainerDependencyResolver($container),
    $container->resolve(IContentNegotiator::class)
);
$response = $apiKernel->handle($request);
(new StreamResponseWriter)->writeResponse($response);
