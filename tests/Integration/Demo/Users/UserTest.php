<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Users;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationSchemeNotFoundException;
use Aphiria\Authentication\IAuthenticator;
use Aphiria\ContentNegotiation\FailedContentNegotiationException;
use Aphiria\ContentNegotiation\MediaTypeFormatters\SerializationException;
use Aphiria\DependencyInjection\Container;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Net\Http\IRequest;
use Aphiria\Net\Http\IResponse;
use Aphiria\Security\Identity;
use Aphiria\Security\IdentityBuilder;
use Aphiria\Security\IPrincipal;
use Aphiria\Security\PrincipalBuilder;
use Aphiria\Security\User as Principal;
use App\Demo\Database\GlobalDatabaseSeeder;
use App\Demo\Users\NewUser;
use App\Demo\Users\User;
use App\Tests\Integration\Demo\Auth\MockAuthenticator;
use App\Tests\Integration\IntegrationTestCase;
use Exception;

class UserTest extends IntegrationTestCase
{
    private MockAuthenticator $authenticator;
    /** @var list<User> The list of users to delete at the end of each test */
    private array $createdUsers = [];

    protected function setUp(): void
    {
        parent::setUp();

        Container::$globalInstance?->resolve(GlobalDatabaseSeeder::class)->seed();
        $this->createTestingAuthenticator();

        /**
         * TODO
         *
         * Should also create mockable IAuthority
         *      This should probably do some real work, eg looking up policy by name like the real one so that integration tests are slightly more realistic.  This would require some refactoring to either an abstract class or an overridable Authority class.
         * Should update mockable IAuthenticator to likewise to some more realistic lookups to ensure that things like schemes actually exist
         */
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Create an admin user to delete the user with
        $adminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier('foo')
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

    public function testCreatingUsersMakesThemRetrievableAsAdminUser(): void
    {
        $createdUser = $this->createUser();

        // Check that the user can be retrieved
        $adminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier('foo')
                    ->withRoles('admin');
            })->build();
        $response = $this->actingAs($adminUser)->get("/demo/users/{$createdUser->id}");
        $this->assertStatusCodeEquals(200, $response);
        $this->assertParsedBodyEquals($createdUser, $response);
    }

    public function testDeletingAnotherUserAsAdminReturns204(): void
    {
        $createdUser = $this->createUser(false);

        // Try deleting the created user
        $adminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier('foo')
                    ->withRoles('admin');
            })->build();
        $deleteUserResponse = $this->actingAs($adminUser)->delete("/demo/users/$createdUser->id");
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $deleteUserResponse);
    }

    public function testDeletingAnotherUserAsNonAdminReturns403(): void
    {
        $createdUser = $this->createUser();

        // Try deleting the created user
        $nonAdminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier('foo');
            })->build();
        $response = $this->actingAs($nonAdminUser)->delete("/demo/users/$createdUser->id");
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testDeletingNonExistentUserReturns404(): void
    {
        $adminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier('foo')
                    ->withRoles('admin');
            })->build();
        // Pass in a dummy user ID
        $deleteUserResponse = $this->actingAs($adminUser)->delete('/demo/users/0');
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $deleteUserResponse);
    }

    public function testDeletingYourOwnUserReturns204(): void
    {
        $createdUser = $this->createUser(false);

        // Try deleting the created user
        $createdUserPrincipal = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) use ($createdUser) {
                $identity->withNameIdentifier($createdUser->id);
            })->build();
        $response = $this->actingAs($createdUserPrincipal)->delete("/demo/users/$createdUser->id");
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $response);
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $response = $this->actingAs(new Principal(new Identity([])))->get('/demo/users/0');
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $response);
    }

    public function testGettingPagedUsersReturnsForbiddenResponseForNonAdmins(): void
    {
        $nonAdminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier('foo')
                    // TODO: Should I need to be specifying auth scheme here and elsewhere?  Why or why not?
                    // TODO: Should #[Authorize] automatically include #[Authenticate]?  What's the precedence in other frameworks?
                    ->withAuthenticationSchemeName('cookie');
            })->build();
        $response = $this->actingAs($nonAdminUser)->get('/demo/users');
        // TODO: This actually redirects to /access-denied, so it's a 302.  Do I want it to redirect?
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testGettingPagedUsersReturnsSuccessfullyForAdmins(): void
    {
        // Since our testing database may have many users, we can't know for sure what the result will be
        // So, just test that this returns ok
        $this->createUser();
        $adminUser = (new PrincipalBuilder('example.com'))
            ->withIdentity(function (IdentityBuilder $identity) {
                $identity->withNameIdentifier('foo')
                    ->withRoles('admin')
                    ->withAuthenticationSchemeName('cookie');
            })->build();
        $response = $this->actingAs($adminUser)->get('/demo/users');
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertNotEmpty($response->getBody()?->readAsString());
    }


    /**
     * Mocks the next authentication call to act as the input principal
     *
     * @param IPrincipal $user The principal to act as for authentication calls
     * @param list<string>|string|null $schemeNames The scheme name or names to authenticate with, or null if using the default scheme
     * @throws AuthenticationSchemeNotFoundException Thrown if any of the scheme names could not be found
     */
    protected function actingAs(IPrincipal $user, array|string $schemeNames = null): static
    {
        $this->authenticator->actingAs($user, $schemeNames);

        return $this;
    }

    // TODO: Remove this once I've done some PoC work
    protected function createTestingAuthenticator(): void
    {
        $this->authenticator = Container::$globalInstance?->resolve(MockAuthenticator::class);

        // Bind these mocks to the container
        Container::$globalInstance?->bindInstance(IAuthenticator::class, $this->authenticator);
    }

    /**
     * Creates a user for use in integration tests
     *
     * @param bool $cleanUp Whether to clean up the user after each test has run
     * @return User The created user
     * @throws FailedContentNegotiationException|SerializationException|HttpException|Exception Thrown if there was an error creating the user
     */
    private function createUser(bool $cleanUp = true): User
    {
        // Create a unique email address so we do not have collisions
        $newUser = new NewUser(\bin2hex(\random_bytes(8)) . '@example.com', 'password');
        /** @var User $createdUser */
        $createdUser = $this->readResponseBodyAs(User::class, $this->post('/demo/users', body: $newUser));

        if ($cleanUp) {
            // Make sure we clean this user up later
            $this->createdUsers[] = $createdUser;
        }

        return $createdUser;
    }
}
