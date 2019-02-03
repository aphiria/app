<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Aphiria\Routing\Builders\RouteBuilderRegistry;
use App\Application\Http\Controllers\UserController;
use App\Application\Http\Middleware\Authorization;

/**
 * ----------------------------------------------------------
 * Register your application's routes
 * ----------------------------------------------------------
 *
 * Note: This file is meant to be included in a Closure in route_matcher.php
 *
 * @var RouteBuilderRegistry $routes
 */
$routes->map('GET', 'users/:id(int)')
    ->toMethod(UserController::class, 'getUserById');
$routes->map('GET', 'users/random')
    ->toMethod(UserController::class, 'getRandomUser');
$routes->map('GET', 'users')
    ->toMethod(UserController::class, 'getAllUsers')
    ->withMiddleware(Authorization::class);
$routes->map('POST', 'users')
    ->toMethod(UserController::class, 'createUser');
$routes->map('POST', 'users/many')
    ->toMethod(UserController::class, 'createManyUsers');
