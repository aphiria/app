<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\Schemes\IAuthenticationSchemeHandler;
use Aphiria\Net\Http\IRequest;
use Aphiria\Security\IPrincipal;

/**
 * Defines the mock authenticator for use in tests
 *
 * TODO: This should likely be refactored into Aphiria
 */
class MockAuthenticator extends UpdatedAuthenticator implements IMockedAuthenticator
{
    /** @var IPrincipal|null The principal we're acting as, or null if we are not acting as anyone */
    private ?IPrincipal $actor = null;
    /** @var list<string>|string|null The scheme name or names the actor is acting as, or null if we are not acting as anyone */
    private array|string|null $actorSchemes = null;

    /**
     * @inheritdoc
     */
    public function actingAs(IPrincipal $user, array|string $schemeNames = null): void
    {
        $this->actor = $user;
        $this->actorSchemes = $schemeNames;
    }

    public function authenticate(IRequest $request, array|string $schemeNames = null): AuthenticationResult
    {
        $authResult = parent::authenticate($request, $this->actorSchemes);
        // We only act as a principal for a single authentication call
        $this->actor = $this->actorSchemes = null;

        return $authResult;
    }

    /**
     * @inheritdoc
     */
    protected function authenticateWithScheme(
        IRequest $request,
        AuthenticationScheme $scheme,
        IAuthenticationSchemeHandler $schemeHandler
    ): AuthenticationResult {
        // If we haven't specified an actor, just continue as normal
        if ($this->actor === null) {
            return parent::authenticateWithScheme($request, $scheme, $schemeHandler);
        }

        // We are acting as a principal, so mock the authentication result
        return AuthenticationResult::pass($this->actor, $scheme->name);
    }
}
