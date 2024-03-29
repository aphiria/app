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
use App\Auth\Binders\AuthBinder;
use App\Auth\Policies\OwnerOrAdminRequirement;
use App\Auth\Policies\OwnerOrAdminRequirementHandler;
use App\Auth\Schemes\BasicAuthenticationHandler;
use App\Auth\Schemes\CookieAuthenticationHandler;

/**
 * Defines the auth module
 */
final class AuthModule extends AphiriaModule
{
    /**
     * @inheritdoc
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new AuthBinder())
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
                OwnerOrAdminRequirement::class,
                new OwnerOrAdminRequirementHandler()
            )
            ->withAuthorizationRequirementHandler(
                $appBuilder,
                RolesRequirement::class,
                new RolesRequirementHandler()
            )
            ->withAuthorizationPolicy(
                $appBuilder,
                new AuthorizationPolicy(
                    'authorized-user-role-granter',
                    new RolesRequirement('admin')
                )
            )
            ->withAuthorizationPolicy(
                $appBuilder,
                new AuthorizationPolicy(
                    'owner-or-admin',
                    new OwnerOrAdminRequirement('admin')
                )
            )
            ->withProblemDetails(
                $appBuilder,
                InvalidCredentialsException::class,
                status: HttpStatusCode::BadRequest
            );
    }
}
