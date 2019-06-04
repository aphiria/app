<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Users\Application\Config;

use Aphiria\Configuration\IApplicationBuilder;
use Aphiria\Configuration\IModuleBuilder;
use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\Routing\Builders\RouteBuilderRegistry;
use Aphiria\Routing\Builders\RouteGroupOptions;
use App\Users\Application\Api\Controllers\UserController;
use App\Users\Application\Api\Middleware\DummyAuthorization;
use App\Users\Application\Bootstrappers\UserServiceBootstrapper;
use App\Users\Application\Console\Commands\UserCountCommand;
use App\Users\Application\Console\Commands\UserCountCommandHandler;
use Opulence\Ioc\IContainer;

/**
 * Defines the example user module builder
 */
final class UserModuleBuilder implements IModuleBuilder
{
    /** @var IContainer The DI container that can resolve dependencies */
    private $container;

    /**
     * @param IContainer $container The DI container that can resolve dependencies
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function build(IApplicationBuilder $appBuilder): void
    {
        $appBuilder->withBootstrappers(function () {
            // Register bootstrappers here
            return [new UserServiceBootstrapper];
        });

        $appBuilder->withCommands(function (CommandRegistry $commands) {
            // Register console commands here
            $commands->registerCommand(
                new UserCountCommand(),
                function () {
                    return $this->container->resolve(UserCountCommandHandler::class);
                }
            );
        });

        $appBuilder->withRoutes(function (RouteBuilderRegistry $routes) {
            // Register routes here
            $routes->group(new RouteGroupOptions('users'), function (RouteBuilderRegistry $routes) {
                $routes->map('GET', '/:id(int)')
                    ->toMethod(UserController::class, 'getUserById');
                $routes->map('GET', '/random')
                    ->toMethod(UserController::class, 'getRandomUser');
                $routes->map('GET', '')
                    ->toMethod(UserController::class, 'getAllUsers')
                    ->withMiddleware(DummyAuthorization::class);
                $routes->map('POST', '')
                    ->toMethod(UserController::class, 'createUser');
                $routes->map('POST', '/many')
                    ->toMethod(UserController::class, 'createManyUsers');
            });
        });
    }
}
