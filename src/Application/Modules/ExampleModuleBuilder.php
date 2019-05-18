<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Application\Modules;

use Aphiria\Configuration\IApplicationBuilder;
use Aphiria\Configuration\IModuleBuilder;
use Aphiria\Console\Commands\CommandBinding;
use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Aphiria\Routing\Builders\RouteBuilderRegistry;
use Aphiria\Routing\Builders\RouteGroupOptions;
use App\Application\Console\Commands\GreetCommand;
use App\Application\Http\Controllers\UserController;
use App\Application\Http\Middleware\Authorization;

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
        $appBuilder->withBootstrappers(function () {
            // Register bootstrappers here
        });

        $appBuilder->withCommands(function (CommandRegistry $commands) {
            // Register console commands here
            $commands->registerManyCommands([
                new CommandBinding(
                    new GreetCommand(),
                    function () {
                        return function (Input $input, IOutput $output) {
                            $output->writeln("Hello, {$input->arguments['name']}");
                        };
                    }
                )
            ]);
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
                    ->withMiddleware(Authorization::class);
                $routes->map('POST', '')
                    ->toMethod(UserController::class, 'createUser');
                $routes->map('POST', '/many')
                    ->toMethod(UserController::class, 'createManyUsers');
            });
        });
    }
}
