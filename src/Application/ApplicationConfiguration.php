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
use App\Application\Bootstrappers\Http\ContentNegotiatorBootstrapper;
use App\Application\Bootstrappers\Http\ExceptionHandlerBootstrapper;
use App\Application\Bootstrappers\Http\RoutingBootstrapper;
use App\Application\Modules\ExampleModuleBuilder;

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
        $appBuilder->withBootstrappers(function () {
            return [
                ExceptionHandlerBootstrapper::class,
                ContentNegotiatorBootstrapper::class,
                RoutingBootstrapper::class
            ];
        });

        $appBuilder->withModule(new ExampleModuleBuilder);
    }
}
