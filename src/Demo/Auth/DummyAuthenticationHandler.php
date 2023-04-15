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
use App\Demo\IUserService;
use App\Demo\UserNotFoundException;
use RuntimeException;

/**
 * Defines a dummy authentication scheme handler - do not use this in production
 *
 * @implements IAuthenticationSchemeHandler<AuthenticationSchemeOptions>
 */
final class DummyAuthenticationHandler implements IAuthenticationSchemeHandler
{
    /**
     * @param IUserService $users The user service to retrieve users from
     * @param RequestParser $requestParser The request parser to use
     */
    public function __construct(
        private readonly IUserService $users,
        private readonly RequestParser $requestParser = new RequestParser()
    ) {
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
        $queryString = $this->requestParser->parseQueryString($request);

        if (!$queryString->containsKey('letMeIn') || !$queryString->containsKey('userId')) {
            return AuthenticationResult::fail('Could not authenticate user');
        }

        try {
            $currUser = $this->users->getUserById((int)$queryString->get('userId'));
        } catch (UserNotFoundException) {
            return AuthenticationResult::fail('No user with this ID found');
        }

        $claimsIssuer = $scheme->options->claimsIssuer ?? $scheme->name;
        /** @var list<Claim<mixed>> $claims */
        $claims = [
            new Claim(ClaimType::NameIdentifier, $currUser->id, $claimsIssuer),
            new Claim(ClaimType::Email, $currUser->email, $claimsIssuer)
        ];

        return AuthenticationResult::pass(new User(new Identity($claims)));
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
