<?php

declare(strict_types=1);

namespace App\Auth\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Auth\ITokenService;
use App\Auth\SqlTokenService;

/**
 * Defines the auth binder
 */
final class AuthBinder extends Binder
{
    /**
     * @inheritdoc
     */
    public function bind(IContainer $container): void
    {
        $container->bindClass(ITokenService::class, SqlTokenService::class);
    }
}
