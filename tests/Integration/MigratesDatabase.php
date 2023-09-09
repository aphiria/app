<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Exception;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Defines methods for migrating databases in integration tests
 */
trait MigratesDatabase
{
    /**
     * Migrates the database
     *
     * @throws Exception Thrown if the database could not be migrated
     */
    protected function migrateDatabase(): void
    {
        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $app->run(new StringInput('migrate'), new NullOutput());
    }
}
