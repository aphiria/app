<?php

declare(strict_types=1);

namespace App\Users;

use Aphiria\Application\IApplicationBuilder;
use Aphiria\Framework\Application\AphiriaModule;
use Aphiria\Net\Http\HttpStatusCode;
use App\Users\Binders\UserServiceBinder;
use Psr\Log\LogLevel;

/**
 * Defines the user
 */
final class UserModule extends AphiriaModule
{
    /**
     * @inheritdoc
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new UserServiceBinder())
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
