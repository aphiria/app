<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Aphiria\Api\ApiKernel;
use Aphiria\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;

$paths = require __DIR__ . '/../../config/paths.php';
require_once "{$paths['vendor']}/autoload.php";
require "{$paths['config.framework']}/environment.php";
require "{$paths['config.framework']}/ioc.php";

$contentNegotiator = require "{$paths['config.framework.http']}/content_negotiator.php";
$negotiatedResponseFactory = new NegotiatedResponseFactory($contentNegotiator);
$exceptionHandler = require "{$paths['config.framework.http']}/exception_handler.php";
$routeMatcher = require "{$paths['config.framework.http']}/route_matcher.php";
$dependencyResolver = require "{$paths['config.framework.http']}/dependency_resolver.php";
require "{$paths['config.framework.http']}/bootstrappers.php";

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
