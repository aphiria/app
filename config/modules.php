<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Configuration\IApplicationBuilder;
use App\Application\Bootstrappers\Http\RoutingBootstrapper;
use App\Application\ModuleBuilders\ExampleModuleBuilder;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

return function (IApplicationBuilder $appBuilder): void {
    $appBuilder->withBootstrappers(function (IBootstrapperRegistry $bootstrappers) {
        // Todo: Will need to be updated to use latest IoC library changes once they're published
        $bootstrappers->registerEagerBootstrapper([RoutingBootstrapper::class]);
    });

    $appBuilder->withModule(new ExampleModuleBuilder);
};
