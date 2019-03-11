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
use Aphiria\Configuration\ApplicationBuilder;
use Aphiria\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;

$paths = require __DIR__ . '/../../config/paths.php';
require_once "{$paths['vendor']}/autoload.php";
require "{$paths['config.framework']}/environment.php";
require "{$paths['config.framework']}/ioc.php";
require "{$paths['config.framework.http']}/bootstrappers.php";

$contentNegotiator = require "{$paths['config.framework.http']}/content_negotiator.php";
$negotiatedResponseFactory = new NegotiatedResponseFactory($contentNegotiator);
$exceptionHandler = require "{$paths['config.framework.http']}/exception_handler.php";
$dependencyResolver = require "{$paths['config.framework.http']}/dependency_resolver.php";
$routeFactory = require "{$paths['config.framework.http']}/route_factory.php";

// Use app builders to finish building our app
// Todo: Is it really necessary that I pass in a command registry here when this is an HTTP app?
$appBuilder = new ApplicationBuilder($bootstrappers, $routeFactory, new \Aphiria\Console\Commands\CommandRegistry());
(require "{$paths['config']}/modules.php")($appBuilder);
$appBuilder->build();

// Explicitly set up our route matcher after all app builders have added our routes
$routeMatcher = require "{$paths['config.framework.http']}/route_matcher.php";

/**
 * ----------------------------------------------------------
 * Handle the request
 * ----------------------------------------------------------
 */
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$exceptionHandler->setRequest($request);
$apiKernel = new ApiKernel(
    $routeMatcher,
    $dependencyResolver,
    $contentNegotiator
);
$response = $apiKernel->handle($request);
(new StreamResponseWriter)->writeResponse($response);
