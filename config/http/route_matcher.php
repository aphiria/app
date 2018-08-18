<?php
/*
 * Opulence
 * 
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
use Opulence\Routing\Builders\RouteBuilderRegistry;
use Opulence\Routing\Caching\FileRouteCache;
use Opulence\Routing\Matchers\RouteMatcher;
use Opulence\Routing\Regexes\Caching\FileGroupRegexCache;
use Opulence\Routing\Regexes\GroupRegexFactory;
use Opulence\Routing\RouteFactory;

/**
 * ----------------------------------------------------------
 * Set up the route matcher
 * ----------------------------------------------------------
 */
$routeFactory = new RouteFactory(
    function (RouteBuilderRegistry $routes) {
        require_once __DIR__ . '/routes.php';
    },
    new FileRouteCache(__DIR__ . '/../../tmp/framework/http/routing/routes.cache.txt')
);
$regexFactory = new GroupRegexFactory(
    $routeFactory->createRoutes(),
    new FileGroupRegexCache(__DIR__ . '/../../tmp/framework/http/routing/regexes.cache.txt')
);

return new RouteMatcher($regexFactory->createRegexes());