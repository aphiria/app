<?php

declare(strict_types=1);

namespace App\Demo\Database\Components;

use Aphiria\Application\IApplicationBuilder;
use Aphiria\DependencyInjection\Container;
use App\Demo\Database\IDatabaseSeeder;
use RuntimeException;

/**
 * Defines the trait for adding database components to your application
 */
trait DatabaseComponents
{
    /**
     * Registers a database seeder or seeders to the application
     *
     * @param IApplicationBuilder $appBuilder The application builder
     * @param class-string<IDatabaseSeeder>|list<class-string<IDatabaseSeeder>> $classNames The name or names of database seeder classes to add
     * @return static For chaining
     * @throws RuntimeException Thrown if the global container instance was not set
     */
    protected function withDatabaseSeeders(IApplicationBuilder $appBuilder, string|array $classNames): static
    {
        if (!$appBuilder->hasComponent(DatabaseSeederComponent::class)) {
            $appBuilder->withComponent(new DatabaseSeederComponent(Container::$globalInstance ?? throw new RuntimeException('Global instance of container not set')));
        }

        $appBuilder->getComponent(DatabaseSeederComponent::class)
            ->withDatabaseSeeders($classNames);

        return $this;
    }
}
