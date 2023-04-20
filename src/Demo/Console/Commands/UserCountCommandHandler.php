<?php

declare(strict_types=1);

namespace App\Demo\Console\Commands;

use Aphiria\Console\Commands\Attributes\Command;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use App\Demo\IUserService;

/**
 * Defines the user count command handler
 */
#[Command('users:count', description: 'An example command that counts the number of users')]
final class UserCountCommandHandler implements ICommandHandler
{
    /**
     * @param IUserService $userService The user service
     */
    public function __construct(private readonly IUserService $userService)
    {
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output): void
    {
        $output->writeln("Number of users: <info>{$this->userService->getNumUsers()}</info>");
    }
}
