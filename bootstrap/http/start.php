<?php
/*
 * Opulence
 * 
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
use Opulence\Api\Handlers\ContainerDependencyResolver;
use Opulence\Api\Handlers\ControllerRequestHandler;
use Opulence\Ioc\Container;
use Opulence\Net\Http\Formatting\ResponseWriter;
use Opulence\Net\Http\RequestFactory;
use Opulence\Net\Http\RequestContextFactory;

require_once __DIR__ . '/../../vendor/autoload.php';

$routeMatcher = require_once __DIR__ . '/../../config/http/route_matcher.php';
$contentNegotiator = require_once __DIR__ . '/../../config/http/content_negotiator.php';
$requestContextFactory = new RequestContextFactory($contentNegotiator);
$container = new Container();
$dependencyResolver = new ContainerDependencyResolver($container);
$exceptionHandler = require_once __DIR__ . '/../../config/http/exceptions.php';

/**
 * ----------------------------------------------------------
 * Handle the request
 * ----------------------------------------------------------
 */
$requestHandler = new ControllerRequestHandler(
    $routeMatcher,
    $dependencyResolver,
    $requestContextFactory,
    null,
    $exceptionHandler
);
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$response = $requestHandler->handle($request);
(new ResponseWriter)->writeResponse($response);