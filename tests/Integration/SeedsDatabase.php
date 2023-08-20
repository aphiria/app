<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\ResolutionException;
use App\Database\GlobalDatabaseSeeder;
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
     * @throws ResolutionException Thrown if the database seeder could not be resolved
     */
    private function seedDatabase(): void
    {
        if (($container = Container::$globalInstance) === null) {
            throw new RuntimeException('No global container instance set');
        }

        $container->resolve(GlobalDatabaseSeeder::class)->seed();
    }
}
