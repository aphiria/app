<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\Authenticator;
use Aphiria\Authentication\Schemes\IAuthenticationSchemeHandler;
use Aphiria\Net\Http\IRequest;
use Aphiria\Security\IPrincipal;
use Closure;

/**
 * Defines the mock authenticator for use in tests
 *
 * TODO: This should likely be refactored into Aphiria
 */
class MockAuthenticator extends Authenticator implements IMockedAuthenticator
{
    /** @var IPrincipal|null The principal we're acting as, or null if we are not acting as anyone */
    private ?IPrincipal $actor = null;

    /**
     * @inheritdoc
     */
    public function actingAs(IPrincipal $user, Closure $callback): mixed
    {
        $this->actor = $user;
        $returnValue = $callback();
        $this->actor = null;

        return $returnValue;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(IRequest $request, array|string $schemeNames = null): AuthenticationResult
    {
        $authResult = parent::authenticate($request, $schemeNames);
        // We only act as a principal for a single authentication call
        $this->actor = null;

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
        if ($this->actor !== null) {
            // Since we aren't actually calling the scheme handler, be sure to set the scheme name for any identities without one
            foreach ($this->actor->getIdentities() as $identity) {
                if ($identity->getAuthenticationSchemeName() === null) {
                    /** @psalm-suppress InternalMethod TODO: Remove this suppression once this logic lives inside Aphiria proper */
                    $identity->setAuthenticationSchemeName($scheme->name);
                }
            }

            return AuthenticationResult::pass($this->actor, $scheme->name);
        }

        return parent::authenticateWithScheme($request, $scheme, $schemeHandler);
    }
}
