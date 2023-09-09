<?php

declare(strict_types=1);

use Aphiria\Application\Configuration\Bootstrappers\DotEnvBootstrapper;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use App\Database\Binders\DatabaseBinder;

require __DIR__ . '/vendor/autoload.php';

/**
 * If the DI container does not exist, assume we are invoking Phinx commands directly from the CLI.
 * This means we need to manually bootstrap and bind a few things.
 */
if (($container = Container::$globalInstance) === null) {
    $container = new Container();
    Container::$globalInstance = $container;
    $container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);

    // Ensure our environment variables are set
    (new DotEnvBootstrapper(__DIR__ . '/.env'))->bootstrap();
    // Ensure our database connection is configured
    (new DatabaseBinder())->bind($container);
}

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => \getenv('APP_ENV'),
        'production' => [
            'name' => 'database',
            'connection' => $container->resolve(PDO::class)
        ],
        'testing' => [
            'name' => 'database',
            'connection' => $container->resolve(PDO::class)
        ],
        'development' => [
            'name' => 'database',
            'connection' => $container->resolve(PDO::class)
        ]
    ],
    'version_order' => 'creation'
];
