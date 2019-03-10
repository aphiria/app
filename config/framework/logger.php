<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

/**
 * ----------------------------------------------------------
 * Create a PSR-3 logger
 * ----------------------------------------------------------
 *
 * Note: You may use any PSR-3 logger you'd like
 * For convenience, the Monolog library is included here
 */
$logger = new Logger('app');
$logger->pushHandler(new ErrorLogHandler());

return $logger;
