<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Users;

use Aphiria\DependencyInjection\Container;
use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Security\Identity;
use Aphiria\Security\PrincipalBuilder;
use Aphiria\Security\User as Principal;
use App\Tests\Integration\Demo\Authenticates;
use App\Tests\Integration\Demo\CreatesUser;
use App\Tests\Integration\Demo\SeedsDatabase;
use App\Tests\Integration\IntegrationTestCase;
use RuntimeException;

class UserTest extends IntegrationTestCase
{
    use Authenticates;
    use CreatesUser;
    use SeedsDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (($container = Container::$globalInstance) === null) {
            throw new RuntimeException('No global container instance set');
        }

        // TODO: Where should this live once the PoC is done?
        $this->seed($container);
        $this->createTestingAuthenticator($container);
    }

    public function testCreatingUsersMakesThemRetrievableAsAdminUser(): void
    {
        $createdUser = $this->createUser();

        // Check that the user can be retrieved
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->get("/demo/users/$createdUser->id"));
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertParsedBodyEquals($createdUser, $response);
    }

    public function testDeletingAnotherUserAsAdminReturns204(): void
    {
        $createdUser = $this->createUser();

        // Try deleting the created user
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->delete("/demo/users/$createdUser->id"));
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $response);
    }

    public function testDeletingAnotherUserAsNonAdminReturns403(): void
    {
        $createdUser = $this->createUser();

        // Try deleting the created user
        $nonAdminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->build();
        $response = $this->actingAs($nonAdminUser, fn () => $this->delete("/demo/users/$createdUser->id"));
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testDeletingNonExistentUserReturns404(): void
    {
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->withRoles('admin')
            ->build();
        // Pass in a dummy user ID
        $response = $this->actingAs($adminUser, fn () => $this->get('/demo/users/0'));
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $response);
    }

    public function testDeletingYourOwnUserReturns204(): void
    {
        $createdUser = $this->createUser();

        // Try deleting the created user
        $createdUserPrincipal = (new PrincipalBuilder('example.com'))->withNameIdentifier($createdUser->id)
            ->build();
        $response = $this->actingAs($createdUserPrincipal, fn () => $this->delete("/demo/users/$createdUser->id"));
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $response);
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $nonAdminUser = new Principal(new Identity([]));
        $response = $this->actingAs($nonAdminUser, fn () => $this->get('/demo/users/0'));
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $response);
    }

    public function testGettingPagedUsersRedirectsToForbiddenPageForNonAdmins(): void
    {
        $nonAdminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->build();
        $response = $this->actingAs($nonAdminUser, fn () => $this->get('/demo/users'));
        $this->assertStatusCodeEquals(HttpStatusCode::Found, $response);
        $this->assertHeaderEquals('/access-denied', $response, 'Location');
    }

    public function testGettingPagedUsersReturnsSuccessfullyForAdmins(): void
    {
        // Since our testing database may have many users, we can't know for sure what the result will be
        // So, just test that this returns ok
        $this->createUser();
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->get('/demo/users'));
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertNotEmpty($response->getBody()?->readAsString());
    }
}
