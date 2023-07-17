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
    /** @var bool Whether or not we're acting as a user */
    private bool $acting = false;
    /** @var IPrincipal|null The principal we're acting as, or null if we are not acting as anyone */
    private ?IPrincipal $actor = null;
    /** @var list<string>|string|null The scheme name or names the actor is acting as, or null if we are not acting as anyone */
    private array|string|null $actorSchemes = null;

    /**
     * @inheritdoc
     */
    public function actingAs(IPrincipal $user, array|string $schemeNames = null): void
    {
        $this->acting = true;
        $this->actor = $user;
        $this->actorSchemes = $schemeNames;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(IRequest $request, array|string $schemeNames = null): AuthenticationResult
    {
        $authResult = parent::authenticate($request, $this->acting ? $this->actorSchemes : $schemeNames);
        // We only act as a principal for a single authentication call
        $this->actor = $this->actorSchemes = null;
        $this->acting = false;

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
        if ($this->acting && $this->actor !== null) {
            return AuthenticationResult::pass($this->actor, $scheme->name);
        }

        return parent::authenticateWithScheme($request, $scheme, $schemeHandler);
    }
}
