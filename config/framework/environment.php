<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

/**
 * ----------------------------------------------------------
 * Load environment config files
 * ----------------------------------------------------------
 *
 * Note:  For performance in production, it's highly suggested
 * you set environment variables on the server itself
 */
$environmentConfigFiles = [
    "{$paths['root']}/.env.app.php"
];

foreach ($environmentConfigFiles as $environmentConfigFile) {
    require $environmentConfigFile;
}
