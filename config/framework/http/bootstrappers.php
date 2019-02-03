<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Caching\FileCache;
use Opulence\Ioc\Bootstrappers\Dispatchers\BootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Dispatchers\IBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Factories\BootstrapperRegistryFactory;
use Opulence\Ioc\Bootstrappers\Factories\CachedBootstrapperRegistryFactory;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * ----------------------------------------------------------
 * Configure the bootstrappers for the HTTP kernel
 * ----------------------------------------------------------
 */
$httpBootstrappers = require "{$paths['config.http']}/bootstrappers.php";
$allBootstrappers = array_merge($globalBootstrappers, $httpBootstrappers);

// If you should cache your bootstrapper registry
if (Environment::getVar('ENV_NAME') === Environment::PRODUCTION) {
    $bootstrapperCache = new FileCache(
        "{$paths['tmp.framework.http']}/cachedBootstrapperRegistry.json"
    );
    $bootstrapperFactory = new CachedBootstrapperRegistryFactory($bootstrapperResolver, $bootstrapperCache);
    $bootstrapperRegistry = $bootstrapperFactory->createBootstrapperRegistry($allBootstrappers);
} else {
    $bootstrapperFactory = new BootstrapperRegistryFactory($bootstrapperResolver);
    $bootstrapperRegistry = $bootstrapperFactory->createBootstrapperRegistry($allBootstrappers);
}

$bootstrapperDispatcher = new BootstrapperDispatcher($container, $bootstrapperRegistry, $bootstrapperResolver);
$container->bindInstance(IBootstrapperRegistry::class, $bootstrapperRegistry);
$container->bindInstance(IBootstrapperDispatcher::class, $bootstrapperDispatcher);