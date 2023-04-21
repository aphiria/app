<?php

declare(strict_types=1);

namespace App\Demo\Users\Console\Commands;

use Aphiria\Console\Commands\Attributes\Command;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use App\Demo\Users\UserSeeder;

/**
 * Defines the user count command handler
 */
#[Command('users:seed', description: 'An example command that seeds the user service')]
final class UserSeederCommandHandler implements ICommandHandler
{
    /**
     * @param UserSeeder $userSeeder What we'll use to seed the user service
     */
    public function __construct(private readonly UserSeeder $userSeeder)
    {
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output): void
    {
        $this->userSeeder->seed();
        $output->writeln('<info>Users seeded</info>');
    }
}
