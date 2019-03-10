<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\Dispatchers\BootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Dispatchers\IBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * ----------------------------------------------------------
 * Configure the bootstrappers for the HTTP kernel
 * ----------------------------------------------------------
 */

// If you should cache your bootstrapper registry
// Todo: How do we cache this like in opulence/project?
$bootstrappers = new BootstrapperRegistry();
$bootstrapperDispatcher = new BootstrapperDispatcher($container, $bootstrappers, $bootstrapperResolver);
$container->bindInstance(IBootstrapperRegistry::class, $bootstrappers);
$container->bindInstance(IBootstrapperDispatcher::class, $bootstrapperDispatcher);
