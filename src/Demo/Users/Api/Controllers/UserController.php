<?php

declare(strict_types=1);

namespace App\Demo\Users\Api\Controllers;

use Aphiria\Api\Controllers\Controller;
use Aphiria\Authentication\Attributes\Authenticate;
use Aphiria\Authorization\Attributes\AuthorizeRoles;
use Aphiria\Authorization\IAuthority;
use Aphiria\Authorization\PolicyNotFoundException;
use Aphiria\Authorization\RequirementHandlerNotFoundException;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\IResponse;
use Aphiria\Routing\Attributes\Delete;
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
     * @param IUserService $users The user service
     * @param IAuthority $authority The authority
     */
    public function __construct(
        private readonly IUserService $users,
        private readonly IAuthority $authority
    ) {
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
        return $this->users->createUser($user);
    }

    /**
     * Deletes a user
     *
     * @param string $id The ID of the user to delete
     * @return IResponse The response
     * @throws HttpException Thrown if the content could not be negotiated
     * @throws PolicyNotFoundException|RequirementHandlerNotFoundException Thrown if there was an error authorizing this request
     */
    #[Delete('/:id'), Authenticate('cookie')]
    public function deleteUser(string $id): IResponse
    {
        try {
            $userToDelete = $this->users->getUserById($id);
        } catch (UserNotFoundException $ex) {
            return $this->notFound();
        }

        /** @psalm-suppress PossiblyNullArgument The user will be set */
        if (!$this->authority->authorize($this->getUser(), 'authorized-user-deleter', $userToDelete)->passed) {
            return $this->forbidden();
        }

        $this->users->deleteUser($id);

        return $this->noContent();
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
    #[Get(''), Authenticate('cookie'), AuthorizeRoles('admin')]
    public function getPagedUsers(int $pageNumber = 0, int $pageSize = 100): IResponse
    {
        return $this->ok($this->users->getPagedUsers($pageNumber, $pageSize));
    }

    /**
     * Gets a user with the input ID
     *
     * @param string $id The ID of the user to get
     * @return UserViewModel The user with the input ID
     * @throws UserNotFoundException Thrown if there was no user with the input ID
     */
    #[Get(':id'), Authenticate('cookie')]
    public function getUserById(string $id): UserViewModel
    {
        return $this->users->getUserById($id);
    }
}
