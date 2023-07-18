<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\Authentication\IAuthenticator;
use Aphiria\Security\IPrincipal;

/**
 * Defines the interface for mocked authenticators to implement
 *
 * TODO: This should likely be refactored into Aphiria
 */
interface IMockedAuthenticator extends IAuthenticator
{
    /**
     * Mocks the next authentication call to act as the input principal
     *
     * @param IPrincipal $user The principal to act as for authentication calls
     */
    public function actingAs(IPrincipal $user): void;
}
