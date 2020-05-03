<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Tests\Integration\Demo;

use App\Demo\User;
use App\Tests\Integration\IntegrationTestCase;

class UserTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed a user
        $this->post('http://localhost/users', [], new User(123, 'foo@bar.com'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->delete('http://localhost/users');
    }

    public function testGettingAllUsers(): void
    {
        $response = $this->get('http://localhost/users?letMeIn=1');
        $this->assertStatusCodeEquals(200, $response);
        $this->assertParsedBodyEquals(
            [new User(123, 'foo@bar.com')],
            $response
        );
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $response = $this->get('http://localhost/users/-1');
        $this->assertStatusCodeEquals(404, $response);
    }
}
