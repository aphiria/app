<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

use Opulence\Api\Handlers\ContainerDependencyResolver;
use Opulence\Api\Handlers\ControllerRequestHandler;
use Opulence\Ioc\Container;
use Opulence\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Opulence\Net\Http\RequestFactory;
use Opulence\Net\Http\ResponseWriter;

require_once __DIR__ . '/../../vendor/autoload.php';

$contentNegotiator = require_once __DIR__ . '/../../config/http/content_negotiator.php';
$negotiatedResponseFactory = new NegotiatedResponseFactory($contentNegotiator);
$exceptionHandler = require_once __DIR__ . '/../../config/http/exception_handler.php';
$routeMatcher = require_once __DIR__ . '/../../config/http/route_matcher.php';
$container = new Container();
$dependencyResolver = new ContainerDependencyResolver($container);

/**
 * ----------------------------------------------------------
 * Handle the request
 * ----------------------------------------------------------
 */
$requestHandler = new ControllerRequestHandler(
    $routeMatcher,
    $dependencyResolver,
    $contentNegotiator
);
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$exceptionHandler->setRequest($request);
$response = $requestHandler->handle($request);
(new ResponseWriter)->writeResponse($response);
