<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Exception;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Defines methods for seeding databases in integration tests
 */
trait SeedsDatabase
{
    /**
     * Seeds the database
     *
     * @throws Exception Thrown if the database could not be seeded
     */
    protected function seedDatabase(): void
    {
        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $app->run(new StringInput('seed:run'), new NullOutput());
    }
}
