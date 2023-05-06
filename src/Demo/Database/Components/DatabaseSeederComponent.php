<?php

declare(strict_types=1);

namespace App\Demo\Database\Components;

use Aphiria\Application\IComponent;
use Aphiria\DependencyInjection\IContainer;
use App\Demo\Database\GlobalDatabaseSeeder;
use App\Demo\Database\IDatabaseSeeder;

/**
 * Defines a component that will let us configure database seeders
 */
final class DatabaseSeederComponent implements IComponent
{
    /** @var list<class-string<IDatabaseSeeder>> The list of registered database seeders */
    private array $databaseSeeders = [];

    /**
     * @param IContainer $container The DI container
     */
    public function __construct(private readonly IContainer $container)
    {
    }

    /**
     * @inheritdoc
     */
    public function build(): void
    {
        $globalDatabaseSeeder = new GlobalDatabaseSeeder(
            \array_map(
                fn (string $classString) => $this->container->resolve($classString),
                $this->databaseSeeders
            )
        );
        $this->container->bindInstance(GlobalDatabaseSeeder::class, $globalDatabaseSeeder);
    }

    /**
     * Adds a database seeder to the application
     *
     * @param class-string<IDatabaseSeeder>|list<class-string<IDatabaseSeeder>> $classNames The name or names of database seeders to register
     * @return static For chaining
     */
    public function withDatabaseSeeder(array|string $classNames): static
    {
        $classNames = \is_string($classNames) ? [$classNames] : $classNames;
        $this->databaseSeeders = [...$this->databaseSeeders, ...$classNames];

        return $this;
    }
}
