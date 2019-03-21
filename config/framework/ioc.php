<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

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

/**
 * ----------------------------------------------------------
 * Set some bindings
 * ----------------------------------------------------------
 *
 * You don't do this in a bootstrapper because you need them
 * bound before bootstrappers are even run
 */
$container->bindInstance(IContainer::class, $container);
