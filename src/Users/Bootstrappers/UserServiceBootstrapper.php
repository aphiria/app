<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Users\Bootstrappers;

use App\Users\FileUserService;
use App\Users\IUserService;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines the user service bootstrapper
 */
final class UserServiceBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $userService = new FileUserService(__DIR__ . '/../../../tmp/users/users.json');
        $container->bindInstance(IUserService::class, $userService);
    }
}
