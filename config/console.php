<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Aphiria\Configuration\Console\IConsoleApplicationBuilder;
use Aphiria\Console\Commands\CommandRegistry;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

return function (IConsoleApplicationBuilder $appBuilder): void {
    $appBuilder->withBootstrappers(function (IBootstrapperRegistry $bootstrappers) {
        // Register bootstrappers here
    });

    $appBuilder->withCommands(function (CommandRegistry $commands) {
        // Register commands here
    });
};
