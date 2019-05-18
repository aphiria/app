<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App;

use Aphiria\Configuration\IApplicationBuilder;
use App\Http\Bootstrappers\ContentNegotiatorBootstrapper;
use App\Http\Bootstrappers\ExceptionHandlerBootstrapper;
use App\Http\Bootstrappers\RoutingBootstrapper;
use App\Logging\Bootstrappers\LoggerBootstrapper;
use App\Users\UserModuleBuilder;
use Opulence\Ioc\IContainer;

/**
 * Defines the application configuration
 */
final class ApplicationConfiguration
{
    /**
     * Configures the application's components
     *
     * @param IApplicationBuilder $appBuilder The app builder to use when configuring the application
     * @param IContainer $container The DI container that can resolve dependencies
     */
    public static function configure(IApplicationBuilder $appBuilder, IContainer $container): void
    {
        $appBuilder->withBootstrappers(function () {
            return [
                ExceptionHandlerBootstrapper::class,
                LoggerBootstrapper::class,
                ContentNegotiatorBootstrapper::class,
                RoutingBootstrapper::class
            ];
        });

        $appBuilder->withModule(new UserModuleBuilder($container));
    }
}
