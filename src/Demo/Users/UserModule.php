<?php

declare(strict_types=1);

namespace App\Demo\Users;

use Aphiria\Application\IApplicationBuilder;
use Aphiria\Framework\Application\AphiriaModule;
use Aphiria\Net\Http\HttpStatusCode;
use App\Demo\Database\Components\DatabaseComponents;
use App\Demo\Users\Binders\UserServiceBinder;
use Psr\Log\LogLevel;

/**
 * Defines the user
 */
final class UserModule extends AphiriaModule
{
    use DatabaseComponents;

    /**
     * @inheritdoc
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new UserServiceBinder())
            ->withDatabaseSeeders($appBuilder, SqlUserSeeder::class)
            ->withProblemDetails(
                $appBuilder,
                UserNotFoundException::class,
                status: HttpStatusCode::NotFound
            )
            ->withProblemDetails(
                $appBuilder,
                InvalidPageException::class,
                status: HttpStatusCode::BadRequest
            )
            ->withLogLevelFactory(
                $appBuilder,
                UserNotFoundException::class,
                static fn (UserNotFoundException $ex): string => LogLevel::INFO
            );
    }
}
