<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Users;

use Aphiria\Authentication\AuthenticationSchemeNotFoundException;
use Aphiria\Authentication\IAuthenticator;
use Aphiria\ContentNegotiation\FailedContentNegotiationException;
use Aphiria\ContentNegotiation\MediaTypeFormatters\SerializationException;
use Aphiria\DependencyInjection\Container;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Security\IdentityBuilder;
use Aphiria\Security\IPrincipal;
use Aphiria\Security\PrincipalBuilder;
use App\Demo\Database\GlobalDatabaseSeeder;
use App\Demo\Users\NewUser;
use App\Demo\Users\User;
use App\Tests\Integration\Demo\Auth\IMockedAuthenticator;
use App\Tests\Integration\Demo\Auth\MockAuthenticator;
use Exception;
use RuntimeException;

/**
 * Defines the trait for creating users in integration tests
 */
trait CreateUser
{
    private IMockedAuthenticator $authenticator;
    /** @var list<User> The list of users to delete at the end of each test */
    private array $createdUsers = [];

    protected function setUp(): void
    {
        parent::setUp();

        // TODO: Where should this live once the PoC is done?
        Container::$globalInstance?->resolve(GlobalDatabaseSeeder::class)->seed();
        $this->createTestingAuthenticator();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Create an admin user to delete the user with
        $adminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier(1)
                    ->withRoles('admin')
                    ->withAuthenticationSchemeName('cookie');
            })->build();

        // Clean up after our integration tests
        foreach ($this->createdUsers as $user) {
            $this->assertStatusCodeEquals(
                HttpStatusCode::NoContent,
                $this->actingAs($adminUser)->delete("/demo/users/$user->id")
            );
        }
    }

    /**
     * Mocks the next authentication call to act as the input principal
     *
     * @param IPrincipal $user The principal to act as for authentication calls
     * @param list<string>|string|null $schemeNames The scheme name or names to authenticate with, or null if using the default scheme
     * @throws AuthenticationSchemeNotFoundException Thrown if any of the scheme names could not be found
     * TODO: This should be moved into the integration test once the PoC is done
     */
    protected function actingAs(IPrincipal $user, array|string $schemeNames = null): static
    {
        $this->authenticator->actingAs($user, $schemeNames);

        return $this;
    }

    // TODO: Remove this once I've done some PoC work
    // TODO: I think this logic probably should live in an Aphiria-provided binder
    protected function createTestingAuthenticator(): void
    {
        if (($container = Container::$globalInstance) === null) {
            throw new RuntimeException('Global container instance not set');
        }

        $this->authenticator = $container->resolve(MockAuthenticator::class);

        // Bind these mocks to the container
        Container::$globalInstance?->bindInstance(IAuthenticator::class, $this->authenticator);
    }

    /**
     * Creates a user for use in integration tests
     *
     * @param bool $cleanUp Whether to clean up the user after each test has run
     * @param string $password The user's password
     * @return User The created user
     * @throws FailedContentNegotiationException|SerializationException|HttpException|Exception Thrown if there was an error creating the user
     */
    private function createUser(bool $cleanUp = true, string $password = 'password'): User
    {
        // Create a unique email address so we do not have collisions
        $newUser = new NewUser(\bin2hex(\random_bytes(8)) . '@example.com', $password);
        /** @var User $createdUser */
        $createdUser = $this->readResponseBodyAs(User::class, $this->post('/demo/users', body: $newUser));

        if ($cleanUp) {
            // Make sure we clean this user up later
            $this->createdUsers[] = $createdUser;
        }

        return $createdUser;
    }

}
