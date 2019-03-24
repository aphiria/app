<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Opulence\Environments\Environment;

/**
 * ----------------------------------------------------------
 * Set environment metadata
 * ----------------------------------------------------------
 */
Environment::setVar('ENV_NAME', Environment::DEVELOPMENT);
Environment::setVar('APP_URL', 'http://localhost');