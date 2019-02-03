<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

namespace App\Application\Http\Controllers;

use App\Domain\Users\User;
use Aphiria\Api\Controllers\Controller;
use Aphiria\Net\Http\HttpHeaders;
use Aphiria\Net\Http\IHttpResponseMessage;
use Aphiria\Net\Http\Response;
use Aphiria\Net\Http\StringBody;

/**
 * Defines the user controller
 */
class UserController extends Controller
{
    public function createManyUsers(): array
    {
        return $this->readRequestBodyAs(User::class . '[]');
    }

    public function createUser(User $user): User
    {
        return $user;
    }

    public function getAllUsers(): IHttpResponseMessage
    {
        return $this->ok([new User(123, 'foo@bar.com'), new User(456, 'bar@baz.com')]);
    }

    public function getRandomUser(): IHttpResponseMessage
    {
        $headers = new HttpHeaders();
        $headers->add('Content-Type', 'application/json');
        $id = \random_int(1, 999);
        $body = new StringBody('{"id":' . $id . ',"email":"foo@bar.com"}');

        return new Response(200, $headers, $body);
    }

    public function getUserById(int $id): User
    {
        return new User($id, 'foo@bar.com');
    }
}
