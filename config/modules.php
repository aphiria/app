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
use App\Application\ModuleBuilders\ExampleModuleBuilder;

return function (IApplicationBuilder $appBuilder): void {
    $appBuilder->withModule(new ExampleModuleBuilder);
};
