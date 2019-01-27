<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

use Opulence\Ioc\Bootstrappers\BootstrapperResolver;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;

/**
 * ----------------------------------------------------------
 * Set up the bootstrappers
 * ----------------------------------------------------------
 *
 * The following starts settings up the bootstrappers
 */
$container = new Container();
$bootstrapperResolver = new BootstrapperResolver();
$globalBootstrapperPath = "{$paths['config']}/bootstrappers.php";
$globalBootstrappers = require $globalBootstrapperPath;

/**
 * ----------------------------------------------------------
 * Set some bindings
 * ----------------------------------------------------------
 *
 * You don't do this in a bootstrapper because you need them
 * bound before bootstrappers are even run
 */
$container->bindInstance(IContainer::class, $container);