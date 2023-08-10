<?php

declare(strict_types=1);

namespace App\Auth\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Auth\ITokenService;
use App\Auth\SqlTokenService;

/**
 * Defines the auth service binder
 */
final class AuthServiceBinder extends Binder
{
    /**
     * @inheritdoc
     */
    public function bind(IContainer $container): void
    {
        // Configure the token service
        $container->bindClass(ITokenService::class, SqlTokenService::class);
    }
}
