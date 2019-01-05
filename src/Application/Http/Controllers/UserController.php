<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace App\Application\Http\Controllers;

use App\Domain\Users\User;
use Opulence\Api\Controller;
use Opulence\Net\Http\HttpHeaders;
use Opulence\Net\Http\IHttpResponseMessage;
use Opulence\Net\Http\Response;
use Opulence\Net\Http\StringBody;

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

    public function getRandomUser(): IHttpResponseMessage
    {
        $headers = new HttpHeaders();
        $headers->add('Content-Type', 'application/json');
        $id = \random_int(1, 999);
        $body = new StringBody('{"id":' . $id . ',"email":"foo@bar.com"}');

        return new Response(200, $headers, $body);
    }

    public function getAllUsers(): IHttpResponseMessage
    {
        return $this->ok([new User(123, 'foo@bar.com'), new User(456, 'bar@baz.com')]);
    }

    public function getUserById(int $id): User
    {
        return new User($id, 'foo@bar.com');
    }
}
