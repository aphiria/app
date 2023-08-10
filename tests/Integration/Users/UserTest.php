<?php

declare(strict_types=1);

namespace App\Tests\Integration\Users;

use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Security\IPrincipal;
use Aphiria\Security\PrincipalBuilder;
use Aphiria\Security\User as Principal;
use App\Tests\Integration\CreatesUser;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Integration\MigratesDatabase;
use App\Tests\Integration\SeedsDatabase;
use App\Users\User;
use PHPUnit\Framework\Attributes\DataProvider;

class UserTest extends IntegrationTestCase
{
    use CreatesUser;
    use MigratesDatabase;
    use SeedsDatabase;

    /**
     * Provides invalid page size and number parameters
     *
     * @return list<array{0: int, 1: int}> The invalid page size and numbers
     */
    public static function provideInvalidPageSizes(): array
    {
        return [
            [0, 1],
            [101, 1],
            [1, -1]
        ];
    }

    public function testCreatingUserAsAdminRetainsRoles(): void
    {
        $createdUser = $this->createUser(createAsAdmin: true, roles: ['foo']);
        $response = $this->actingAs(
            self::createPrincipalFromUser($createdUser),
            fn () => $this->get("/users/$createdUser->id")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertParsedBodyEquals($createdUser, $response);
    }

    public function testCreatingUserAsNonAdminRemovesRoles(): void
    {
        $createdUser = $this->createUser(roles: ['foo']);
        $createdUserWithoutRoles = new User($createdUser->id, $createdUser->email, []);
        $response = $this->actingAs(
            self::createPrincipalFromUser($createdUser),
            fn () => $this->get("/users/$createdUser->id")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertParsedBodyEquals($createdUserWithoutRoles, $response);
    }

    public function testDeletingAnotherUserAsAdminReturns204(): void
    {
        $createdUser = $this->createUser();
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->delete("/users/$createdUser->id"));
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $response);
    }

    public function testDeletingAnotherUserAsNonAdminReturns403(): void
    {
        $createdUser = $this->createUser();
        $nonAdminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->build();
        $response = $this->actingAs($nonAdminUser, fn () => $this->delete("/users/$createdUser->id"));
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testDeletingNonExistentUserReturns403(): void
    {
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->delete('/users/0'));
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testDeletingYourOwnUserReturns204(): void
    {
        $createdUser = $this->createUser();
        $response = $this->actingAs(
            self::createPrincipalFromUser($createdUser),
            fn () => $this->delete("/users/$createdUser->id")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $response);
    }

    public function testGettingInvalidUserReturns403(): void
    {
        $user = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->build();
        $response = $this->actingAs($user, fn () => $this->get('/users/0'));
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testGettingPagedUsersRedirectsToForbiddenPageForNonAdmins(): void
    {
        $nonAdminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->build();
        $response = $this->actingAs($nonAdminUser, fn () => $this->get('/users'));
        $this->assertStatusCodeEquals(HttpStatusCode::Found, $response);
        $this->assertHeaderEquals('/access-denied', $response, 'Location');
    }

    public function testGettingPagedUsersReturnsSuccessfullyForAdmins(): void
    {
        $this->createUser();
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->get('/users'));
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        // Integration tests may have created many users, so just check that the endpoint returns a non-empty list
        $this->assertParsedBodyPassesCallback(
            $response,
            User::class . '[]',
            fn (array $users): bool => \count($users) > 0
        );
    }

    /**
     * @param int $pageSize The page size to test
     * @param int $pageNumber The page number to test
     */
    #[DataProvider('provideInvalidPageSizes')]
    public function testGettingPagedUsersWithInvalidPageSizesReturnsBadRequests(int $pageSize, int $pageNumber): void
    {
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs(
            $adminUser,
            fn () => $this->get("/users?pageSize=$pageSize&pageNumber=$pageNumber")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::BadRequest, $response);
    }

    public function testGettingUserDoesNotWorkForNonOwnerNonAdmin(): void
    {
        $createdUser = $this->createUser();
        $nonAdminNonOwnerUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->build();
        $response = $this->actingAs(
            $nonAdminNonOwnerUser,
            fn () => $this->get("/users/$createdUser->id")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testGettingUserWorksForAdmin(): void
    {
        $createdUser = $this->createUser();
        $adminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(0)
            ->withRoles('admin')
            ->build();
        $response = $this->actingAs(
            $adminUser,
            fn () => $this->get("/users/$createdUser->id")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertParsedBodyEquals($createdUser, $response);
    }

    public function testGettingUserWorksForOwner(): void
    {
        $createdUser = $this->createUser();
        $response = $this->actingAs(
            self::createPrincipalFromUser($createdUser),
            fn () => $this->get("/users/$createdUser->id")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertParsedBodyEquals($createdUser, $response);
    }

    /**
     * Creates a principal from a user
     *
     * @param User $user The user to create a principal from
     * @return IPrincipal The created principal
     */
    private static function createPrincipalFromUser(User $user): IPrincipal
    {
        return (new PrincipalBuilder('example.com'))->withNameIdentifier($user->id)
            ->withEmail($user->email)
            ->withRoles($user->roles)
            ->build();
    }
}
