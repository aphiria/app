<?php

declare(strict_types=1);

namespace App\Demo;

use Aphiria\Application\IApplicationBuilder;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Framework\Application\AphiriaModule;
use Aphiria\Net\Http\HttpStatusCode;
use App\Demo\Auth\DummyAuthenticationHandler;
use App\Demo\Binders\UserServiceBinder;
use Psr\Log\LogLevel;

/**
 * Defines the demo module
 */
final class DemoModule extends AphiriaModule
{
    /**
     * @inheritdoc
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new UserServiceBinder())
            ->withAuthenticationScheme(
                $appBuilder,
                new AuthenticationScheme('dummy', DummyAuthenticationHandler::class)
            )
            ->withProblemDetails(
                $appBuilder,
                UserNotFoundException::class,
                status: HttpStatusCode::NotFound
            )
            ->withLogLevelFactory(
                $appBuilder,
                UserNotFoundException::class,
                static fn (UserNotFoundException $ex): string => LogLevel::INFO
            );
    }
}
