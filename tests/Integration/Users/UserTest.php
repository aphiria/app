<?php

declare(strict_types=1);

namespace App\Tests\Integration\Users;

use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Security\Identity;
use Aphiria\Security\PrincipalBuilder;
use Aphiria\Security\User as Principal;
use App\Tests\Integration\CreatesUser;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Integration\SeedsDatabase;
use App\Users\User;
use PHPUnit\Framework\Attributes\DataProvider;

class UserTest extends IntegrationTestCase
{
    use CreatesUser;
    use SeedsDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
    }

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

    public function testCreatingUsersMakesThemRetrievableAsAdminUser(): void
    {
        $createdUser = $this->createUser();
        $adminUser = (new PrincipalBuilder('example.com'))->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->get("/users/$createdUser->id"));
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertParsedBodyEquals($createdUser, $response);
    }

    public function testDeletingAnotherUserAsAdminReturns204(): void
    {
        $createdUserId = $this->createUser()->id;
        $adminUser = (new PrincipalBuilder('example.com'))->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->delete("/users/$createdUserId"));
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $response);
    }

    public function testDeletingAnotherUserAsNonAdminReturns403(): void
    {
        $createdUserId = $this->createUser()->id;
        $nonAdminUser = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->build();
        $response = $this->actingAs($nonAdminUser, fn () => $this->delete("/users/$createdUserId"));
        $this->assertStatusCodeEquals(HttpStatusCode::Forbidden, $response);
    }

    public function testDeletingNonExistentUserReturns404(): void
    {
        $adminUser = (new PrincipalBuilder('example.com'))->withRoles('admin')
            ->build();
        $response = $this->actingAs($adminUser, fn () => $this->get('/users/0'));
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $response);
    }

    public function testDeletingYourOwnUserReturns204(): void
    {
        $createdUserId = $this->createUser()->id;
        $createdUser = (new PrincipalBuilder('example.com'))->withNameIdentifier($createdUserId)
            ->build();
        $response = $this->actingAs($createdUser, fn () => $this->delete("/users/$createdUserId"));
        $this->assertStatusCodeEquals(HttpStatusCode::NoContent, $response);
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $user = new Principal(new Identity());
        $response = $this->actingAs($user, fn () => $this->get('/users/0'));
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $response);
    }

    public function testGettingPagedUsersRedirectsToForbiddenPageForNonAdmins(): void
    {
        $nonAdminUser = new Principal(new Identity());
        $response = $this->actingAs($nonAdminUser, fn () => $this->get('/users'));
        $this->assertStatusCodeEquals(HttpStatusCode::Found, $response);
        $this->assertHeaderEquals('/access-denied', $response, 'Location');
    }

    public function testGettingPagedUsersReturnsSuccessfullyForAdmins(): void
    {
        $this->createUser();
        $adminUser = (new PrincipalBuilder('example.com'))->withRoles('admin')
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
        $adminUser = (new PrincipalBuilder('example.com'))->withRoles('admin')
            ->build();
        $response = $this->actingAs(
            $adminUser,
            fn () => $this->get("/users?pageSize=$pageSize&pageNumber=$pageNumber")
        );
        $this->assertStatusCodeEquals(HttpStatusCode::BadRequest, $response);
    }
}
