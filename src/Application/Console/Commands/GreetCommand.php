<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Application\Console\Commands;

use Aphiria\Console\Commands\Command;
use Aphiria\Console\Input\Argument;
use Aphiria\Console\Input\ArgumentTypes;

/**
 * Defines an example command
 */
final class GreetCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            'example:greet',
            [new Argument('name', ArgumentTypes::REQUIRED, 'The name to greet')],
            [],
            'An example command that greets a person'
        );
    }
}
