<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Api\ContainerDependencyResolver;

/**
 * ----------------------------------------------------------
 * Set up the dependency resolver
 * ----------------------------------------------------------
 *
 * This will resolve all dependencies in your controllers and middleware
 */
return new ContainerDependencyResolver($container);
