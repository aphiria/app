<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use RuntimeException;

/**
 * Defines methods for seeding databases in integration tests
 */
trait SeedsDatabase
{
    /**
     * Seeds the database
     *
     * @throws RuntimeException Thrown if the global container instance was not set
     */
    private function seedDatabase(): void
    {
        // TODO: Need to make this work with Phinx
    }
}
