<?php

declare(strict_types=1);

namespace App\Demo\Auth;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\AuthenticationSchemeOptions;
use Aphiria\Authentication\Schemes\IAuthenticationSchemeHandler;
use Aphiria\Net\Http\Formatting\RequestParser;
use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Net\Http\IRequest;
use Aphiria\Net\Http\IResponse;
use Aphiria\Security\Claim;
use Aphiria\Security\ClaimType;
use Aphiria\Security\Identity;
use Aphiria\Security\User;
use RuntimeException;

/**
 * Defines a dummy authentication scheme handler - do not use this in production
 *
 * @implements IAuthenticationSchemeHandler<AuthenticationSchemeOptions>
 */
final class DummyAuthenticationHandler implements IAuthenticationSchemeHandler
{
    /**
     * @param RequestParser $requestParser The request parser to use
     */
    public function __construct(private readonly RequestParser $requestParser = new RequestParser())
    {
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if this was used in production
     */
    public function authenticate(IRequest $request, AuthenticationScheme $scheme): AuthenticationResult
    {
        if (\getenv('APP_ENV') === 'production') {
            throw new RuntimeException('Do not use this handler in production - it is for demo purposes only');
        }

        // Note: This is just a really silly demo - do not use this in production
        if ($this->requestParser->parseQueryString($request)->containsKey('letMeIn')) {
            $user = new User(new Identity([new Claim(ClaimType::NameIdentifier, 1, 'example.com')]));

            return AuthenticationResult::pass($user);
        }

        return AuthenticationResult::fail('Could not authenticate user');
    }

    /**
     * @inheritdoc
     */
    public function challenge(IRequest $request, IResponse $response, AuthenticationScheme $scheme): void
    {
        $response->setStatusCode(HttpStatusCode::Unauthorized);
    }

    /**
     * @inheritdoc
     */
    public function forbid(IRequest $request, IResponse $response, AuthenticationScheme $scheme): void
    {
        $response->setStatusCode(HttpStatusCode::Forbidden);
    }
}
