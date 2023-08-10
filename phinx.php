<?php

declare(strict_types=1);

use Aphiria\Application\Configuration\Bootstrappers\DotEnvBootstrapper;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use App\Database\Binders\DatabaseBinder;

require __DIR__ . '/vendor/autoload.php';

// Create our DI container
$container = new Container();
Container::$globalInstance = $container;
$container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);

// Ensure our environment variables are set
(new DotEnvBootstrapper(__DIR__ . '/.env'))->bootstrap();
// Ensure our database connection is configured
(new DatabaseBinder())->bind($container);

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'name' => 'database',
            'connection' => $container->resolve(PDO::class)
        ]
    ],
    'version_order' => 'creation'
];
