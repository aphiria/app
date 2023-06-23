<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Users;

use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Security\Identity;
use Aphiria\Security\IdentityBuilder;
use Aphiria\Security\PrincipalBuilder;
use Aphiria\Security\User as Principal;
use App\Tests\Integration\IntegrationTestCase;

class UserTest extends IntegrationTestCase
{
    use CreateUser;

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
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
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
}
