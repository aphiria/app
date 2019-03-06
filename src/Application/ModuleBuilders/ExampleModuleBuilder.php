<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/app/console/blob/master/LICENSE.md
 */

namespace App\Application\ModuleBuilders;

use Aphiria\Configuration\IApplicationBuilder;
use Aphiria\Configuration\IModuleBuilder;
use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\Routing\Builders\RouteBuilderRegistry;
use App\Application\Http\Controllers\UserController;
use App\Application\Http\Middleware\Authorization;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the example module builder
 */
final class ExampleModuleBuilder implements IModuleBuilder
{
    /**
     * @inheritdoc
     */
    public function build(IApplicationBuilder $appBuilder): void
    {
        $appBuilder->withBootstrappers(function (IBootstrapperRegistry $bootstrappers) {
            // Register bootstrappers here
        });

        $appBuilder->withCommands(function (CommandRegistry $commands) {
            // Register console commands here
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
    }
}
