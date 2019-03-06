<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Aphiria\Configuration\Http\IHttpApplicationBuilder;
use Aphiria\Routing\Builders\RouteBuilderRegistry;
use App\Application\Http\Controllers\UserController;
use App\Application\Http\Middleware\Authorization;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

return function (IHttpApplicationBuilder $appBuilder): void {
    $appBuilder->withBootstrappers(function (IBootstrapperRegistry $bootstrappers) {
        // Register bootstrappers here
    });

    $appBuilder->withRoutes(function (RouteBuilderRegistry $routes) {
        // Register routes here
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
    });
};
