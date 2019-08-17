<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Users\Api\Controllers;

use Aphiria\Api\Controllers\Controller;
use Aphiria\Net\Http\HttpHeaders;
use Aphiria\Net\Http\IHttpResponseMessage;
use Aphiria\Net\Http\Response;
use Aphiria\Net\Http\StringBody;
use Aphiria\RouteAnnotations\Annotations\Get;
use Aphiria\RouteAnnotations\Annotations\Middleware;
use Aphiria\RouteAnnotations\Annotations\Post;
use Aphiria\RouteAnnotations\Annotations\RouteGroup;
use App\Users\Api\Middleware\DummyAuthorization;
use App\Users\IUserService;
use App\Users\User;

/**
 * Defines the user controller
 * @RouteGroup("users")
 */
final class UserController extends Controller
{
    /** @var IUserService The user service */
    private IUserService $userService;

    /**
     * @param IUserService $userService The user service
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Post("many")
     */
    public function createManyUsers(): array
    {
        $users = $this->readRequestBodyAs(User::class . '[]');

        return $this->userService->createManyUsers($users);
    }

    /**
     * @Post("")
     */
    public function createUser(User $user): User
    {
        return $this->userService->createUser($user);
    }

    /**
     * @Get("")
     * @Middleware(DummyAuthorization::class)
     */
    public function getAllUsers(): IHttpResponseMessage
    {
        return $this->ok($this->userService->getAllUsers());
    }

    /**
     * @Get("random")
     */
    public function getRandomUser(): IHttpResponseMessage
    {
        $user = $this->userService->getRandomUser();

        if ($user === null) {
            return $this->notFound();
        }

        $headers = new HttpHeaders();
        $headers->add('Content-Type', 'application/json');
        $body = new StringBody('{"id":' . $user->getId() . ',"email":"' . $user->getEmail() . '"}');

        return new Response(200, $headers, $body);
    }

    /**
     * @Get(":id(int)")
     */
    public function getUserById(int $id): User
    {
        return $this->userService->getUserById($id);
    }
}
