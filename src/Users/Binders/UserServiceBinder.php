<?php

declare(strict_types=1);

namespace App\Users\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Users\IUserService;
use App\Users\SqlUserService;

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
    }
}
