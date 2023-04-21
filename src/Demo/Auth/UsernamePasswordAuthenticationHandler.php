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

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\AuthenticationSchemeOptions;
use Aphiria\Authentication\Schemes\BasicAuthenticationHandler;
use Aphiria\Authentication\Schemes\BasicAuthenticationOptions;
use Aphiria\Authentication\Schemes\IAuthenticationSchemeHandler;
use Aphiria\ContentNegotiation\IContentNegotiator;
use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Net\Http\IRequest;
use Aphiria\Net\Http\IResponse;
use Aphiria\Security\Claim;
use Aphiria\Security\ClaimType;
use Aphiria\Security\Identity;
use Aphiria\Security\User;
use App\Demo\Users\IUserService;

/**
 * Defines the username/password authentication handler
 *
 * @extends  BasicAuthenticationHandler<BasicAuthenticationOptions>
 */
final class UsernamePasswordAuthenticationHandler extends BasicAuthenticationHandler
{
    /**
     * @param IUserService $users The user service
     */
    public function __construct(private readonly IUserService $users)
    {
    }

    protected function createAuthenticationResultFromCredentials(
        string $username,
        string $password,
        IRequest $request,
        AuthenticationScheme $scheme
    ): AuthenticationResult {
        if (($user = $this->users->getUserByEmailAndPassword($username, $password)) === null) {
            return AuthenticationResult::fail('Invalid credentials');
        }

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
}
