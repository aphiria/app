<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Application;

use Aphiria\Configuration\IApplicationBuilder;
use App\Application\Bootstrappers\Http\ExceptionHandlerBootstrapper;
use App\Application\Bootstrappers\Http\RoutingBootstrapper;
use App\Application\Modules\ExampleModuleBuilder;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the application configuration
 */
final class ApplicationConfiguration
{
    /**
     * Configures the application's components
     *
     * @param IApplicationBuilder $appBuilder The app builder to use when configuring the application
     */
    public static function configure(IApplicationBuilder $appBuilder): void
    {
        $appBuilder->withBootstrappers(function (IBootstrapperRegistry $bootstrappers) {
            // Todo: Will need to be updated to use latest IoC library changes once they're published
            $bootstrappers->registerEagerBootstrapper([
                ExceptionHandlerBootstrapper::class,
                RoutingBootstrapper::class
            ]);
        });

        $appBuilder->withModule(new ExampleModuleBuilder);
    }
}
