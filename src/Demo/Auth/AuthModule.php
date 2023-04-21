<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2023 David Young
 * @license   https://github.com/aphiria/app/blob/1.x/LICENSE.md
 */

declare(strict_types=1);

namespace App\Demo\Auth;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\Schemes\BasicAuthenticationOptions;
use Aphiria\Authentication\Schemes\CookieAuthenticationOptions;
use Aphiria\Framework\Application\AphiriaModule;
use Aphiria\Net\Http\Headers\SameSiteMode;
use App\Demo\Auth\Binders\AuthServiceBinder;

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
        $this->withBinders($appBuilder, new AuthServiceBinder())
            ->withAuthenticationScheme(
                $appBuilder,
                new AuthenticationScheme(
                    'token',
                    TokenAuthenticationHandler::class,
                    new CookieAuthenticationOptions(
                        cookieName: 'authToken',
                        cookieMaxAge: 360,
                        cookiePath: '/',
                        cookieDomain: (string)\getenv('APP_COOKIE_DOMAIN'),
                        cookieIsSecure: (bool)\getenv('APP_COOKIE_SECURE'),
                        cookieIsHttpOnly: true,
                        cookieSameSite: SameSiteMode::Strict,
                        loginPagePath: '/login',
                        forbiddenPagePath: '/access-denied',
                        claimsIssuer: (string)\getenv('APP_COOKIE_DOMAIN')
                    )
                )
            )
            ->withAuthenticationScheme(
                $appBuilder,
                new AuthenticationScheme(
                    'usernamePassword',
                    UsernamePasswordAuthenticationHandler::class,
                    new BasicAuthenticationOptions((string)\getenv('APP_URL'))
                )
            );
    }
}
