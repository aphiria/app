<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

use Opulence\Api\ContainerDependencyResolver;

/**
 * ----------------------------------------------------------
 * Set up the dependency resolver
 * ----------------------------------------------------------
 *
 * This will resolve all dependencies in your controllers and middleware
 */
return new ContainerDependencyResolver($container);