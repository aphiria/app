<?php

declare(strict_types=1);

namespace App\Demo\Database\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use PDO;

/**
 * Defines the database binder
 */
final class DatabaseBinder extends Binder
{
    /**
     * @inheritdoc
     */
    public function bind(IContainer $container): void
    {
        $container->bindFactory(
            PDO::class,
            fn () => new PDO('sqlite:' . __DIR__ . '/../../../../' . (string)\getenv('DB_PATH'))
        );
    }
}
