<?php

declare(strict_types=1);

namespace App\Database\Binders;

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
        $dbPath = (string)\getenv('DB_PATH');
        $dsn = 'sqlite:' . ($dbPath === ':memory:' ? $dbPath : __DIR__ . "/../../../$dbPath");
        $container->bindFactory(
            PDO::class,
            fn () => new PDO($dsn),
            true
        );
    }
}
