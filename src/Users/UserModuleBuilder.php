<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Users;

use Aphiria\Configuration\IApplicationBuilder;
use Aphiria\Configuration\IModuleBuilder;
use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Exceptions\ExceptionResponseFactoryRegistry;
use Aphiria\Net\Http\ContentNegotiation\INegotiatedResponseFactory;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IHttpRequestMessage;
use App\Users\Console\Commands\UserCountCommand;
use App\Users\Console\Commands\UserCountCommandHandler;
use App\Users\Bootstrappers\UserServiceBootstrapper;

/**
 * Defines the example user module builder
 */
final class UserModuleBuilder implements IModuleBuilder
{
    /** @var IContainer The DI container that can resolve dependencies */
    private IContainer $container;

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
        $appBuilder->withBootstrappers(fn () => [
            new UserServiceBootstrapper
        ]);

        $appBuilder->withConsoleCommands(function (CommandRegistry $commands) {
            $commands->registerCommand(
                new UserCountCommand(),
                fn () => $this->container->resolve(UserCountCommandHandler::class)
            );
        });

        $appBuilder->withComponent(
            'exceptionResponseFactories',
            fn (ExceptionResponseFactoryRegistry $factories) => $factories->registerFactory(
                UserNotFoundException::class,
                fn (UserNotFoundException $ex, ?IHttpRequestMessage $request, INegotiatedResponseFactory $responseFactory)
                    => $responseFactory->createResponse($request, HttpStatusCodes::HTTP_NOT_FOUND)
            )
        );

        /*
        $appBuilder->withComponent('routes', function (RouteBuilderRegistry $routes) {
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
        */
    }
}
