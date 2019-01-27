<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

use Opulence\Api\ApiKernel;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Caching\FileCache;
use Opulence\Ioc\Bootstrappers\Factories\{BootstrapperRegistryFactory, CachedBootstrapperRegistryFactory};
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\Dispatchers\{BootstrapperDispatcher, IBootstrapperDispatcher};
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

/**
 * ----------------------------------------------------------
 * Configure the bootstrappers for the HTTP kernel
 * ----------------------------------------------------------
 */
$httpBootstrapperPath = "{$paths['config.http']}/bootstrappers.php";
$httpBootstrappers = require $httpBootstrapperPath;
$allBootstrappers = array_merge($globalBootstrappers, $httpBootstrappers);

// If you should cache your bootstrapper registry
if (Environment::getVar('ENV_NAME') === Environment::PRODUCTION) {
    $bootstrapperCache = new FileCache(
        "{$paths['tmp.framework.http']}/cachedBootstrapperRegistry.json"
    );
    $bootstrapperFactory = new CachedBootstrapperRegistryFactory($bootstrapperResolver, $bootstrapperCache);
    $bootstrapperRegistry = $bootstrapperFactory->createBootstrapperRegistry($allBootstrappers);
} else {
    $bootstrapperFactory = new BootstrapperRegistryFactory($bootstrapperResolver);
    $bootstrapperRegistry = $bootstrapperFactory->createBootstrapperRegistry($allBootstrappers);
}

$bootstrapperDispatcher = new BootstrapperDispatcher($container, $bootstrapperRegistry, $bootstrapperResolver);
$container->bindInstance(IBootstrapperRegistry::class, $bootstrapperRegistry);
$container->bindInstance(IBootstrapperDispatcher::class, $bootstrapperDispatcher);

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
