<?php

declare(strict_types=1);

namespace App\Demo\Database;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Framework\Application\AphiriaModule;
use App\Demo\Database\Binders\DatabaseBinder;

/**
 * Defines the database module
 */
final class DatabaseModule extends AphiriaModule
{
    /**
     * @inheritdoc
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new DatabaseBinder());
    }
}
