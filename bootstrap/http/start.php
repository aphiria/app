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
use Aphiria\Configuration\ApplicationBuilder;
use Aphiria\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;
use Aphiria\Routing\Matchers\IRouteMatcher;

$paths = require __DIR__ . '/../../config/paths.php';
require_once "{$paths['vendor']}/autoload.php";
require "{$paths['config.framework']}/environment.php";
require "{$paths['config.framework']}/ioc.php";

$contentNegotiator = require "{$paths['config.framework.http']}/content_negotiator.php";
$negotiatedResponseFactory = new NegotiatedResponseFactory($contentNegotiator);
$exceptionHandler = require "{$paths['config.framework.http']}/exception_handler.php";
$dependencyResolver = require "{$paths['config.framework.http']}/dependency_resolver.php";

// Use app builders to finish building our app
$appBuilder = new ApplicationBuilder($container);
(require "{$paths['config']}/modules.php")($appBuilder);
$appBuilder->build();

/**
 * ----------------------------------------------------------
 * Handle the request
 * ----------------------------------------------------------
 */
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$exceptionHandler->setRequest($request);
$apiKernel = new ApiKernel(
    $container->resolve(IRouteMatcher::class),
    new ContainerDependencyResolver($container),
    $contentNegotiator
);
$response = $apiKernel->handle($request);
(new StreamResponseWriter)->writeResponse($response);
