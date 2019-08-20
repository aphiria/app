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
use Aphiria\Net\Http\HttpException;
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
use App\Users\UserNotFoundException;

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
     * Creates many users
     *
     * @return User[] The list of created users
     * @throws HttpException Thrown if the request body could not be read
     * @Post("many")
     */
    public function createManyUsers(): array
    {
        $users = $this->readRequestBodyAs(User::class . '[]');

        return $this->userService->createManyUsers($users);
    }

    /**
     * Creates a single user
     *
     * @param User $user The user to create
     * @return User The created user
     * @Post("")
     */
    public function createUser(User $user): User
    {
        return $this->userService->createUser($user);
    }

    /**
     * Gets all users
     *
     * @return IHttpResponseMessage The response containing all users
     * @throws HttpException Thrown if there was an error creating the response
     * @Get("")
     * @Middleware(DummyAuthorization::class)
     */
    public function getAllUsers(): IHttpResponseMessage
    {
        return $this->ok($this->userService->getAllUsers());
    }

    /**
     * Gets a random user
     *
     * @return IHttpResponseMessage The response containing the user
     * @throws HttpException Thrown if there was an error creating the response
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
     * Gets a user with the input ID
     *
     * @param int $id The ID of the user to get
     * @return User The user with the input ID
     * @throws UserNotFoundException Thrown if there was no user with the input ID
     * @Get(":id(int)")
     */
    public function getUserById(int $id): User
    {
        return $this->userService->getUserById($id);
    }
}
