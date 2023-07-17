<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo;

use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\ResolutionException;
use App\Demo\Database\GlobalDatabaseSeeder;

/**
 * Defines methods for seeding databases in integration tests
 */
trait SeedsDatabase
{
    /**
     * Seeds the database
     *
     * @param IContainer $container The DI container
     * @throws ResolutionException Thrown if the database seeder could not be resolved
     */
    private function seed(IContainer $container): void
    {
        $container->resolve(GlobalDatabaseSeeder::class)->seed();
    }
}
