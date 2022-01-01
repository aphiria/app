<?php

declare(strict_types=1);

namespace App\Demo\Api\Controllers;

use Aphiria\Api\Controllers\Controller;
use Aphiria\Authentication\Attributes\Authenticate;
use Aphiria\Net\Http\Headers;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\IResponse;
use Aphiria\Net\Http\Response;
use Aphiria\Net\Http\StringBody;
use Aphiria\Routing\Attributes\Delete;
use Aphiria\Routing\Attributes\Get;
use Aphiria\Routing\Attributes\Post;
use Aphiria\Routing\Attributes\RouteGroup;
use App\Demo\IUserService;
use App\Demo\User;
use App\Demo\UserNotFoundException;

/**
 * Defines the user controller
 */
#[RouteGroup('demo/users')]
final class UserController extends Controller
{
    /**
     * @param IUserService $userService The user service
     */
    public function __construct(private readonly IUserService $userService)
    {
    }

    /**
     * Creates many users
     *
     * @return list<User> The list of created users
     * @throws HttpException Thrown if the request body could not be read
     */
    #[Post('many')]
    public function createManyUsers(): array
    {
        // Demonstrate how to read the body as an array of models
        /** @var list<User> $users */
        $users = $this->readRequestBodyAs(User::class . '[]');

        return $this->userService->createManyUsers($users);
    }

    /**
     * Creates a single user
     *
     * @param User $user The user to create
     * @return User The created user
     */
    #[Post('')]
    public function createUser(User $user): User
    {
        // Demonstrate how to use content negotiation on request and response bodies
        return $this->userService->createUser($user);
    }

    /**
     * Deletes all the users
     */
    #[Delete('')]
    public function deleteAllUsers(): void
    {
        $this->userService->deleteAllUsers();
    }

    /**
     * Gets all users
     *
     * @return IResponse The response containing all users
     * @throws HttpException Thrown if there was an error creating the response
     */
    #[Get(''), Authenticate('dummy')]
    public function getAllUsers(): IResponse
    {
        // Demonstrate how to use controller helper methods to create a response
        return $this->ok($this->userService->getAllUsers());
    }

    /**
     * Gets a random user
     *
     * @return IResponse The response containing the user
     * @throws HttpException Thrown if there was an error creating the response
     */
    #[Get('random')]
    public function getRandomUser(): IResponse
    {
        $user = $this->userService->getRandomUser();

        if ($user === null) {
            return $this->notFound();
        }

        // Demonstrate how to manually create a response
        $headers = new Headers();
        $headers->add('Content-Type', 'application/json');
        $body = new StringBody('{"id":' . $user->id . ',"email":"' . $user->email . '"}');

        return new Response(200, $headers, $body);
    }

    /**
     * Gets a user with the input ID
     *
     * @param int $id The ID of the user to get
     * @return User The user with the input ID
     * @throws UserNotFoundException Thrown if there was no user with the input ID
     */
    #[Get(':id(int)')]
    public function getUserById(int $id): User
    {
        // Demonstrate how to use route variables and response body negotiation
        return $this->userService->getUserById($id);
    }
}
