<?php

declare(strict_types=1);

namespace App\Demo\Auth;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\Schemes\BasicAuthenticationHandler as BaseBasicAuthenticationHandler;
use Aphiria\Net\Http\IRequest;
use Aphiria\Security\PrincipalBuilder;
use App\Demo\Users\IUserService;

/**
 * Defines the basic authentication handler
 */
final class BasicAuthenticationHandler extends BaseBasicAuthenticationHandler
{
    /**
     * @param IUserService $users The user service
     */
    public function __construct(private readonly IUserService $users)
    {
    }

    /**
     * @inheritdoc
     */
    protected function createAuthenticationResultFromCredentials(
        string $username,
        string $password,
        IRequest $request,
        AuthenticationScheme $scheme
    ): AuthenticationResult {
        if (($user = $this->users->getUserByEmailAndPassword($username, $password)) === null) {
            return AuthenticationResult::fail('Invalid credentials', $scheme->name);
        }

        return AuthenticationResult::pass(
            (new PrincipalBuilder($scheme->options->claimsIssuer ?? $scheme->name))->withNameIdentifier($user->id)
                ->withEmail($user->email)
                ->withRoles($user->roles)
                ->withAuthenticationSchemeName($scheme->name)
                ->build(),
            $scheme->name
        );
    }
}
