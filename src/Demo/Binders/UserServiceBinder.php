<?php

declare(strict_types=1);

namespace App\Demo\Binders;

use Aphiria\DependencyInjection\Binders\Binder;
use Aphiria\DependencyInjection\IContainer;
use App\Demo\FileUserService;
use App\Demo\IUserService;

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
        $filePath = __DIR__ . '/../../../tmp/demo/users-' . (string)\getenv('APP_ENV') . '.json';
        $userService = new FileUserService($filePath);
        $container->bindInstance(IUserService::class, $userService);
    }
}
