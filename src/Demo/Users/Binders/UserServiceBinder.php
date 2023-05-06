<?php

declare(strict_types=1);

namespace App\Demo\Users\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Demo\Users\IUserService;
use App\Demo\Users\SqlUserService;

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
        // Configure the user service
        $container->bindClass(IUserService::class, SqlUserService::class, resolveAsSingleton: true);
    }
}
