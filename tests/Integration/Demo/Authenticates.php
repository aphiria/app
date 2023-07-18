<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo;

use Aphiria\Authentication\IAuthenticator;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\ResolutionException;
use Aphiria\Security\IPrincipal;
use App\Tests\Integration\Demo\Auth\IMockedAuthenticator;
use App\Tests\Integration\Demo\Auth\MockAuthenticator;

/**
 * Defines methods for authenticating in integration tests
 */
trait Authenticates
{
    /** @var IMockedAuthenticator The mock authenticator to use in tests */
    private IMockedAuthenticator $authenticator;

    /**
     * Mocks the next authentication call to act as the input principal
     *
     * @param IPrincipal $user The principal to act as for authentication calls
     * TODO: This should be moved into the integration test once the PoC is done
     */
    protected function actingAs(IPrincipal $user): static
    {
        $this->authenticator->actingAs($user);

        return $this;
    }

    /**
     * Creates the testing authenticator
     *
     * @param IContainer $container The DI container
     * @throws ResolutionException Thrown if the authenticator could not be resolved
     * TODO: Remove this once I've done some PoC work
     * TODO: I think this logic probably should live in an Aphiria-provided binder
     */
    protected function createTestingAuthenticator(IContainer $container): void
    {
        $this->authenticator = $container->resolve(MockAuthenticator::class);

        // Bind these mocks to the container
        $container->bindInstance(IAuthenticator::class, $this->authenticator);
    }

}
