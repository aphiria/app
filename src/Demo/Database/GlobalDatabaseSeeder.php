<?php

declare(strict_types=1);

namespace App\Demo\Database;

/**
 * Defines the global database seeder that contains all other seeders
 */
final class GlobalDatabaseSeeder implements IDatabaseSeeder
{
    /**
     * @param list<IDatabaseSeeder> $databaseSeeders The list of database seeders
     */
    public function __construct(private readonly array $databaseSeeders)
    {
    }

    /**
     * @inheritdoc
     */
    public function seed(): void
    {
        foreach ($this->databaseSeeders as $databaseSeeder) {
            $databaseSeeder->seed();
        }
    }
}
