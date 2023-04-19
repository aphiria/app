<?php

declare(strict_types=1);

namespace App\Demo\Auth;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\Schemes\CookieAuthenticationHandler as BaseCookieAuthenticationHandler;
use Aphiria\Net\Http\IRequest;
use Aphiria\Security\Claim;
use Aphiria\Security\ClaimType;
use Aphiria\Security\Identity;
use Aphiria\Security\IPrincipal;
use Aphiria\Security\User;
use App\Demo\Users\IUserService;
use App\Demo\Users\UserNotFoundException;

/**
 * Defines a cookie authentication scheme handler
 *
 * @extends BaseCookieAuthenticationHandler
 */
final class CookieAuthenticationHandler extends BaseCookieAuthenticationHandler
{
    /**
     * @param ITokenService $tokens The token service to create/retrieve tokens from
     * @param IUserService $users The user service to retrieve users from
     */
    public function __construct(
        private readonly ITokenService $tokens,
        private readonly IUserService $users
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     * @throws UserNotFoundException Thrown if no user was found with the retrieved user ID
     */
    protected function createAuthenticationResultFromCookie(string $cookieValue, IRequest $request, AuthenticationScheme $scheme): AuthenticationResult
    {
        if (($userId = $this->tokens->getUserIdFromToken($cookieValue)) === null) {
            return AuthenticationResult::fail('Invalid token');
        }

        $user = $this->users->getUserById($userId);
        $claimsIssuer = $scheme->options->claimsIssuer ?? $scheme->name;
        $claims = [
            new Claim(ClaimType::NameIdentifier, $user->id, $claimsIssuer),
            new Claim(ClaimType::Email, $user->email, $claimsIssuer)
        ];

        foreach ($user->roles as $role) {
            $claims[] = new Claim(ClaimType::Role, $role, $claimsIssuer);
        }

        /** @var list<Claim<mixed>> $claims */
        return AuthenticationResult::pass(
            new User(new Identity($claims, $scheme->name))
        );
    }

    /**
     * @inheritdoc
     */
    protected function getCookieValueFromUser(IPrincipal $user, AuthenticationScheme $scheme): string|int|float
    {
        return $this->tokens->createToken($user->getPrimaryIdentity()?->getNameIdentifier());
    }
}
