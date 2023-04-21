<?php

declare(strict_types=1);

namespace App\Demo\Users\Api\Controllers;

use Aphiria\Api\Controllers\Controller;
use Aphiria\Authentication\Attributes\Authenticate;
use Aphiria\Authorization\Attributes\AuthorizeRoles;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\IResponse;
use Aphiria\Routing\Attributes\Get;
use Aphiria\Routing\Attributes\Post;
use Aphiria\Routing\Attributes\RouteGroup;
use App\Demo\Users\InvalidPageException;
use App\Demo\Users\IUserService;
use App\Demo\Users\NewUser;
use App\Demo\Users\UserNotFoundException;
use App\Demo\Users\UserViewModel;

/**
 * Defines the user controller
 */
#[RouteGroup('/demo/users')]
final class UserController extends Controller
{
    /**
     * @param IUserService $userService The user service
     */
    public function __construct(private readonly IUserService $userService)
    {
    }

    /**
     * Creates a user
     *
     * @param NewUser $user The user to create
     * @return UserViewModel The created user
     */
    #[Post('')]
    public function createUser(NewUser $user): UserViewModel
    {
        // Demonstrate how to use content negotiation on request and response bodies
        return $this->userService->createUser($user);
    }

    /**
     * Gets a page of users
     *
     * @param int $pageNumber The page number to retrieve
     * @param int $pageSize The page size
     * @return IResponse The response containing all users
     * @throws HttpException Thrown if there was an error creating the response
     * @throws InvalidPageException Thrown if the pagination parameters were invalid
     */
    #[Get(''), Authenticate('token'), AuthorizeRoles('admin')]
    public function getPagedUsers(int $pageNumber = 0, int $pageSize = 100): IResponse
    {
        // Demonstrate how to use controller helper methods to create a response
        return $this->ok($this->userService->getPagedUsers($pageNumber, $pageSize));
    }

    /**
     * Gets a user with the input ID
     *
     * @param string $id The ID of the user to get
     * @return UserViewModel The user with the input ID
     * @throws UserNotFoundException Thrown if there was no user with the input ID
     */
    #[Get(':id'), Authenticate('token')]
    public function getUserById(string $id): UserViewModel
    {
        // Demonstrate how to use route variables and response body negotiation
        return $this->userService->getUserById($id);
    }
}
