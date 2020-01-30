<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Demo\Console\Commands;

use Aphiria\Console\Commands\Annotations\Command;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use App\Demo\IUserService;

/**
 * Defines the user count command handler
 *
 * @Command("users:count", description="An example command that counts the number of users")
 */
final class UserCountCommandHandler implements ICommandHandler
{
    /** @var IUserService The user service */
    private IUserService $userService;

    /**
     * @param IUserService $userService The user service
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $output->writeln("Number of users: <info>{$this->userService->getNumUsers()}</info>");
    }
}
