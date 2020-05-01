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

use Aphiria\Framework\Testing\PhpUnit\IntegrationTestCase;
use App\Demo\User;

class UserTest extends IntegrationTestCase
{
    public function testGettingAllUsers(): void
    {
        $request = $this->requestBuilder->withMethod('GET')
            ->withUri('http://localhost/users?letMeIn=1')
            ->build();
        $response = $this->client->send($request);
        $this->assertStatusCodeEquals(200, $response);
        // TODO: Need to have some setup that creates/deletes users
        $this->assertParsedBodyEquals(
            [new User(192153, 'foo@bar.com')],
            $request,
            $response
        );
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $request = $this->requestBuilder->withMethod('GET')
            ->withUri('http://localhost/users/-1')
            ->build();
        $response = $this->client->send($request);
        $this->assertStatusCodeEquals(404, $response);
    }
}
