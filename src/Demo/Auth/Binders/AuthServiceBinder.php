<?php

declare(strict_types=1);

namespace App\Demo\Auth\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Demo\Auth\FileTokenService;
use App\Demo\Auth\ITokenService;

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
        $filePath = __DIR__ . '/../../../../tmp/demo/tokens-' . (string)\getenv('APP_ENV') . '.json';
        $tokenService = new FileTokenService($filePath);
        $container->bindInstance(ITokenService::class, $tokenService);
    }
}
