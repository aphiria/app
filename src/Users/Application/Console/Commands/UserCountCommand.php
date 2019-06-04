<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Users\Application\Console\Commands;

use Aphiria\Console\Commands\Command;

/**
 * Defines an example command for grabbing the number of users
 */
final class UserCountCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            'users:count',
            [],
            [],
            'An example command that counts the number of users'
        );
    }
}
