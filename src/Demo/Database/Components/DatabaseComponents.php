<?php

declare(strict_types=1);

namespace App\Demo\Database\Components;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\DependencyInjection\Container;
use App\Demo\Database\IDatabaseSeeder;

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
     */
    protected function withDatabaseSeeders(IApplicationBuilder $appBuilder, string|array $classNames): static
    {
        if (!$appBuilder->hasComponent(DatabaseSeederComponent::class)) {
            $appBuilder->withComponent(new DatabaseSeederComponent(Container::$globalInstance));
        }

        $appBuilder->getComponent(DatabaseSeederComponent::class)
            ->withDatabaseSeeders($classNames);

        return $this;
    }
}
