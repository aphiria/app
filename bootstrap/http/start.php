<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

use Opulence\Api\ApiKernel;
use Opulence\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Opulence\Net\Http\{RequestFactory, ResponseWriter};

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
(new ResponseWriter)->writeResponse($response);
