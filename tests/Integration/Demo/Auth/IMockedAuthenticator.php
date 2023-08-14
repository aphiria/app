<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\Authentication\IAuthenticator;
use Aphiria\Security\IPrincipal;
use Closure;

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
     * @template T The return type of the closure
     * @param IPrincipal $user The principal to act as for authentication calls
     * @param Closure(): T $callback The callback that will make calls as the acting principal
     * @return T The return value of the callback
     */
    public function actingAs(IPrincipal $user, Closure $callback): mixed;
}
