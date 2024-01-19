<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Aphiria\Application\IApplication;
use Aphiria\Application\IApplicationBuilder;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Framework\Api\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use App\GlobalModule;
use TypeError;

/**
 * Defines the base integration test case
 */
class IntegrationTestCase extends BaseIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Auto-call certain trait methods if the test case uses them
        $traits = \class_uses($this);
        $traits = $traits === false ? [] : $traits;

        if (isset($traits[MigratesDatabase::class])) {
            /** @psalm-suppress UndefinedMethod This method will exist because it uses the migration trait */
            $this->migrateDatabase();
        }

        if (isset($traits[SeedsDatabase::class])) {
            /** @psalm-suppress UndefinedMethod This method will exist because it uses the seed trait */
            $this->seedDatabase();
        }
    }

    /**
     * @inheritdoc
     */
    protected function createApplication(IContainer $container): IApplication
    {
        $globalModule = new GlobalModule($container);
        $globalModule->bootstrap();
        $appBuilderClass = (string)\getenv('APP_BUILDER_API');

        if (!\class_exists($appBuilderClass) || !\is_subclass_of($appBuilderClass, IApplicationBuilder::class)) {
            throw new TypeError('Environment variable "APP_BUILDER_API" must implement ' . IApplicationBuilder::class);
        }

        return $container->resolve($appBuilderClass)
            ->withModule($globalModule)
            ->build();
    }
}
