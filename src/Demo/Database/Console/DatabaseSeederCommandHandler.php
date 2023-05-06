<?php

declare(strict_types=1);

namespace App\Demo\Database\Console;

use Aphiria\Console\Commands\Attributes\Command;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use App\Demo\Auth\SqlTokenSeeder;
use App\Demo\Users\SqlUserSeeder;

/**
 * Defines the database seeder command handler
 */
#[Command('database:seed', description: 'Seeds the database with data')]
final class DatabaseSeederCommandHandler implements ICommandHandler
{
    /**
     * @param SqlUserSeeder $userSeeder What we'll use to seed the user service
     * @param SqlTokenSeeder $tokenSeeder What we'll use to seed the token service
     */
    public function __construct(private readonly SqlUserSeeder $userSeeder, private readonly SqlTokenSeeder $tokenSeeder)
    {
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output): void
    {
        $output->writeln('Seeding users');
        $this->userSeeder->seed();
        $output->writeln('<info>Users seeded</info>');

        $output->writeln('Seeding tokens');
        $this->tokenSeeder->seed();
        $output->writeln('<info>Tokens seeded</info>');
    }
}
