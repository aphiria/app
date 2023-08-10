<?php

declare(strict_types=1);

namespace App\Users\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Users\IUserService;
use App\Users\SqlUserSeeder;
use App\Users\SqlUserService;
use PDO;

/**
 * Defines the user service binder
 */
final class UserServiceBinder extends Binder
{
    /**
     * @inheritdoc
     */
    public function bind(IContainer $container): void
    {
        $container->bindClass([IUserService::class, SqlUserService::class], SqlUserService::class, resolveAsSingleton: true);
        $container->bindInstance(
            SqlUserSeeder::class,
            new SqlUserSeeder(
                $container->resolve(SqlUserService::class),
                $container->resolve(PDO::class),
                (string)\getenv('USER_DEFAULT_EMAIL'),
                (string)\getenv('USER_DEFAULT_PASSWORD')
            )
        );
    }
}
