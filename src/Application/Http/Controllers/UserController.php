<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace App\Application\Http\Controllers;

use App\Domain\Users\User;
use Opulence\Api\Controller;
use Opulence\Net\Http\HttpException;
use Opulence\Net\Http\HttpStatusCodes;
use Opulence\Net\Http\IHttpResponseMessage;

/**
 * Defines the user controller
 */
class UserController extends Controller
{
    public function createManyUsers(): array
    {
        return $this->readRequestBodyAs(User::class . '[]');
    }

    public function createUser(User $user, bool $override = false): User
    {
        if (!$override && ($user->getId() !== 123 || $user->getEmail() !== 'foo@bar.com')) {
            throw new HttpException(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR, 'Uh oh');
        }

        return $user;
    }

    public function getAllUsers(): IHttpResponseMessage
    {
        return $this->ok([new User(123, 'foo@bar.com'), new User(456, 'bar@baz.com')]);
    }

    public function getUserById(int $id): User
    {
        if ($id === 404) {
            throw new HttpException(HttpStatusCodes::HTTP_NOT_FOUND);
        }

        return new User($id, 'foo@bar.com');
    }
}
