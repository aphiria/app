<?php

declare(strict_types=1);

namespace App\Demo\Users\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Demo\Auth\FileTokenService;
use App\Demo\Auth\ITokenService;
use App\Demo\Users\FileUserService;
use App\Demo\Users\IUserService;

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
        $filePath = __DIR__ . '/../../../../tmp/demo/users-' . (string)\getenv('APP_ENV') . '.json';
        $users = new FileUserService($filePath);
        $container->bindInstance(IUserService::class, $users);
    }
}
