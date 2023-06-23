<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\Authentication\AuthenticationSchemeNotFoundException;
use Aphiria\Authentication\IAuthenticator;
use Aphiria\Security\IPrincipal;

/**
 * Defines the interface for mocked authenticators to implement
 */
interface IMockedAuthenticator extends IAuthenticator
{
    /**
     * Mocks the next authentication call to act as the input principal
     *
     * @param IPrincipal $user The principal to act as for authentication calls
     * @param list<string>|string|null $schemeNames The scheme name or names to authenticate with, or null if using the default scheme
     * @throws AuthenticationSchemeNotFoundException Thrown if any of the scheme names could not be found
     */
    public function actingAs(IPrincipal $user, array|string $schemeNames = null): void;
}
