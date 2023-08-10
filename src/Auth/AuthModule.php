<?php

declare(strict_types=1);

namespace App\Auth;

use Aphiria\Application\IApplicationBuilder;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\Schemes\BasicAuthenticationOptions;
use Aphiria\Authentication\Schemes\CookieAuthenticationOptions;
use Aphiria\Authorization\AuthorizationPolicy;
use Aphiria\Authorization\RequirementHandlers\RolesRequirement;
use Aphiria\Authorization\RequirementHandlers\RolesRequirementHandler;
use Aphiria\Framework\Application\AphiriaModule;
use Aphiria\Net\Http\Headers\SameSiteMode;
use Aphiria\Net\Http\HttpStatusCode;
use App\Auth\Binders\AuthServiceBinder;
use App\Database\Components\DatabaseComponents;

/**
 * Defines the auth module
 */
final class AuthModule extends AphiriaModule
{
    use DatabaseComponents;

    /**
     * @inheritdoc
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new AuthServiceBinder())
            ->withDatabaseSeeders($appBuilder, SqlTokenSeeder::class)
            // Add our default authentication scheme
            ->withAuthenticationScheme(
                $appBuilder,
                new AuthenticationScheme(
                    'cookie',
                    CookieAuthenticationHandler::class,
                    new CookieAuthenticationOptions(
                        cookieName: 'authToken',
                        cookieMaxAge: 3600,
                        cookiePath: '/',
                        cookieDomain: (string)\getenv('APP_COOKIE_DOMAIN'),
                        cookieIsSecure: (bool)\getenv('APP_COOKIE_SECURE'),
                        cookieIsHttpOnly: true,
                        cookieSameSite: SameSiteMode::Strict,
                        loginPagePath: '/login',
                        forbiddenPagePath: '/access-denied',
                        claimsIssuer: (string)\getenv('APP_COOKIE_DOMAIN')
                    )
                ),
                true
            )
            ->withAuthenticationScheme(
                $appBuilder,
                new AuthenticationScheme(
                    'basic',
                    BasicAuthenticationHandler::class,
                    new BasicAuthenticationOptions((string)\getenv('APP_URL'))
                )
            )
            ->withAuthorizationRequirementHandler(
                $appBuilder,
                AuthorizedUserDeleterRequirement::class,
                new AuthorizedUserDeleterRequirementHandler()
            )
            ->withAuthorizationRequirementHandler(
                $appBuilder,
                RolesRequirement::class,
                new RolesRequirementHandler()
            )
            ->withAuthorizationPolicy(
                $appBuilder,
                new AuthorizationPolicy(
                    'authorized-user-deleter',
                    new AuthorizedUserDeleterRequirement('admin')
                )
            )
            ->withProblemDetails(
                $appBuilder,
                InvalidCredentialsException::class,
                status: HttpStatusCode::BadRequest
            );
    }
}
