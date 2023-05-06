<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Users;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\IAuthenticator;
use Aphiria\Authentication\IUserAccessor;
use Aphiria\DependencyInjection\Container;
use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Net\Http\IRequest;
use Aphiria\Net\Http\IResponse;
use Aphiria\Security\Claim;
use Aphiria\Security\ClaimType;
use Aphiria\Security\Identity;
use Aphiria\Security\IPrincipal;
use Aphiria\Security\User;
use App\Demo\Users\NewUser;
use App\Demo\Users\SqlUserSeeder;
use App\Demo\Users\UserViewModel;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\TestWith;

class UserTest extends IntegrationTestCase
{
    private IAuthenticator $authenticator;
    /** @var list<UserViewModel> The list of users to delete at the end of each test */
    private array $createdUsers = [];

    protected function setUp(): void
    {
        parent::setUp();

        // TODO: Figure out how to run ALL seeders (maybe the CLI command??)
        $userSeeder = Container::$globalInstance?->resolve(SqlUserSeeder::class);
        $userSeeder?->seed();
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

        // Clean up after our integration tests
        foreach ($this->createdUsers as $user) {
            // TODO: Needs auth
            $this->delete("/demo/users/$user->id");
        }
    }

    #[TestWith([new NewUser('foo@bar.com', 'foo')])]
    public function testCreatingUsersMakesThemRetrievableAsAdminUser(NewUser $newUser): void
    {
        // Create some users
        $response = $this->post('/demo/users', [], $newUser);
        /** @var UserViewModel $createdUser */
        $createdUser = $this->readResponseBodyAs(UserViewModel::class, $response);
        // Make sure we clean this user up later
        $this->createdUsers[] = $createdUser;

        // Check that the user can be retrieved
        $claims = [
            new Claim(ClaimType::NameIdentifier, 'foo', 'example.com'),
            new Claim(ClaimType::Role, 'admin', 'example.com')
        ];
        $adminUser = new User(new Identity($claims));
        $response = $this->actingAs($adminUser)->get("/demo/users/{$createdUser->id}");
        $this->assertStatusCodeEquals(200, $response);
        $this->assertParsedBodyEquals($createdUser, $response);
    }

    public function testDeletingAnotherUserAsAdminReturns204(): void
    {
        $claims = [
            new Claim(ClaimType::NameIdentifier, 'foo', 'example.com'),
            new Claim(ClaimType::Role, 'admin', 'example.com')
        ];
        $adminUser = new User(new Identity($claims));
        $createUserResponse = $this->post('/demo/users', body: new NewUser('test@example.com', 'password'));
        /** @var UserViewModel $createdUser */
        $createdUser = $this->readResponseBodyAs(UserViewModel::class, $createUserResponse);
        $deleteUserResponse = $this->actingAs($adminUser)->delete("/demo/users/$createdUser->id");
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $deleteUserResponse);
    }

    public function testDeletingAnotherUserAsNonAdminReturns403(): void
    {
        $claims = [
            new Claim(ClaimType::NameIdentifier, 'foo', 'example.com')
        ];
        $nonAdminUser = new User(new Identity($claims));
        // TODO: This is a hard-coded user ID.  Needs to be dynamically created once I've figured out way of grabbing parsed body.
        $response = $this->actingAs($nonAdminUser)->delete('/demo/users/452b0ee6-7597-45a1-83e2-c70f2ad939f0');
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testDeletingNonExistentUserReturns404(): void
    {
        // TODO
    }

    public function testDeletingYourOwnUserReturns204(): void
    {
        // TODO
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $response = $this->get('/demo/users/0');
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $response);
    }

    public function testGettingPagedUsersReturnsForbiddenResponseForNonAdmins(): void
    {
        // TODO
    }

    public function testGettingPagedUsersReturnsSuccessfullyForAdmins(): void
    {
        // TODO
    }

    // TODO: Figure out where to actually put this logic
    // TODO: This should be scoped for a single request, whereas it's setting the user for all subsequent requests.  Maybe it should take a callback with the client call or something?
    protected function actingAs(IPrincipal $user): static
    {
        // TODO: How should this work in real life?  I'd have to check if IAuthenticator is an instance of some new TestAuthentication, which would have that public property, and if so it'd set that property.  However, is that too constraining to impose a specific test authenticator on users?
        $this->authenticator->expectedAuthenticationResult = AuthenticationResult::pass($user);

        return $this;
    }

    // TODO: Remove this once I've done some PoC work
    protected function createTestingAuthenticator(): void
    {
        $this->authenticator = new class (Container::$globalInstance?->resolve(IUserAccessor::class)) implements IAuthenticator {
            public ?AuthenticationResult $expectedAuthenticationResult = null;

            public function __construct(private readonly IUserAccessor $userAccessor)
            {
            }

            public function authenticate(IRequest $request, string $schemeName = null): AuthenticationResult
            {
                if ($this->expectedAuthenticationResult === null) {
                    throw new \Exception('Expected authentication result is not set');
                }

                if ($this->expectedAuthenticationResult->passed) {
                    $this->userAccessor->setUser($this->expectedAuthenticationResult->user, $request);
                }

                return $this->expectedAuthenticationResult;
            }

            public function challenge(IRequest $request, IResponse $response, string $schemeName = null): void
            {
                // TODO: Implement challenge() method.
            }

            public function forbid(IRequest $request, IResponse $response, string $schemeName = null): void
            {
                // TODO: Implement forbid() method.
            }

            public function logIn(
                IPrincipal $user,
                IRequest $request,
                IResponse $response,
                string $schemeName = null
            ): void {
                $this->userAccessor->setUser($user, $request);
            }

            public function logOut(IRequest $request, IResponse $response, string $schemeName = null): void
            {
                $this->userAccessor->setUser(null, $request);
            }
        };

        // Bind these mocks to the container
        Container::$globalInstance?->bindInstance(IAuthenticator::class, $this->authenticator);
    }
}
