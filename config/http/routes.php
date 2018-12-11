<?php
/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
use App\Application\Http\Controllers\UserController;
use Opulence\Routing\Builders\RouteBuilderRegistry;

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
    ->toMethod(UserController::class, 'getAllUsers');
$routes->map('POST', 'users')
    ->toMethod(UserController::class, 'createUser');
$routes->map('POST', 'users/many')
    ->toMethod(UserController::class, 'createManyUsers');
