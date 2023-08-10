<?php

declare(strict_types=1);

namespace App\Database\Console;

use Aphiria\Console\Commands\Attributes\Command;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use App\Database\GlobalDatabaseSeeder;

/**
 * Defines the database seeder command handler
 */
#[Command('database:seed', description: 'Seeds the database with data')]
final class DatabaseSeederCommandHandler implements ICommandHandler
{
    /**
     * @param GlobalDatabaseSeeder $globalDatabaseSeeder The global database seeder
     */
    public function __construct(private readonly GlobalDatabaseSeeder $globalDatabaseSeeder)
    {
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output): void
    {
        $output->writeln('<info>Seeding database...</info>');
        $this->globalDatabaseSeeder->seed();
        $output->writeln('<success>Database seeded</success>');
    }
}
