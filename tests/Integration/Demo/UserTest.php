<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo;

use Aphiria\DependencyInjection\Container;
use Aphiria\Net\Http\HttpStatusCode;
use App\Demo\Users\NewUser;
use App\Demo\Users\UserSeeder;
use App\Demo\Users\UserViewModel;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\TestWith;

class UserTest extends IntegrationTestCase
{
    /** @var list<UserViewModel> The list of users to delete at the end of each test */
    private array $createdUsers = [];

    protected function setUp(): void
    {
        parent::setUp();

        $userSeeder = Container::$globalInstance?->resolve(UserSeeder::class);
        $userSeeder?->seed();
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

    #[TestWith([[new NewUser('foo@bar.com', 'foo')]])]
    public function testCreatingUsersMakesThemRetrievable(NewUser $newUser): void
    {
        // Create some users
        // $this->createdUsers[] = ??? TODO: Need to somehow populate createdUsers with "UserViewModel" objects
        $this->post('/demo/users', [], $newUser);

        // Check that the user can be retrieved
        $response = $this->get("/demo/users/$user->id");
        $this->assertStatusCodeEquals(200, $response);
        $this->assertParsedBodyEquals($user, $response);
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $response = $this->get('/demo/users/0');
        $this->assertStatusCodeEquals(HttpStatusCode::NotFound, $response);
    }
}
