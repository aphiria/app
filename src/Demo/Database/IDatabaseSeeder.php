<?php

declare(strict_types=1);

namespace App\Demo\Database;

use PDOException;

/**
 * Defines the interface for database seeders to implement
 */
interface IDatabaseSeeder
{
    /**
     * Seeds the database
     *
     * @throws PDOException Thrown if there was an error seeding the database
     */
    public function seed(): void;
}
