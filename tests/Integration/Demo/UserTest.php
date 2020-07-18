<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo;

use App\Demo\User;
use App\Tests\Integration\IntegrationTestCase;

class UserTest extends IntegrationTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up after our integration tests
        $this->delete('/users');
    }

    public function testGettingAllUsers(): void
    {
        // Seed some users
        $this->post('/users', [], new User(123, 'foo@bar.com'));
        $this->post('/users', [], new User(456, 'baz@qux.com'));
        // Get the users
        $response = $this->get('/users?letMeIn=1');
        $this->assertStatusCodeEquals(200, $response);
        $this->assertParsedBodyEquals(
            [new User(123, 'foo@bar.com'), new User(456, 'baz@qux.com')],
            $response
        );
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $response = $this->get('/users/-1');
        $this->assertStatusCodeEquals(404, $response);
    }
}
