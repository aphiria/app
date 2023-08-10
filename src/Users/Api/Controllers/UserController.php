<?php

declare(strict_types=1);

namespace App\Users\Api\Controllers;

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
use Aphiria\Security\User as Principal;
use App\Users\InvalidPageException;
use App\Users\IUserService;
use App\Users\NewUser;
use App\Users\User;
use App\Users\UserNotFoundException;

/**
 * Defines the user controller
 */
#[RouteGroup('/users')]
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
     * @return User The created user
     */
    #[Post('')]
    public function createUser(NewUser $user): User
    {
        $authResult = $this->authority->authorize($this->getUser() ?? new Principal([]), 'authorized-user-role-giver');

        return $this->users->createUser($user, $authResult->passed);
    }

    /**
     * Deletes a user
     *
     * @param int $id The ID of the user to delete
     * @return IResponse The response
     * @throws HttpException Thrown if the content could not be negotiated
     * @throws PolicyNotFoundException|RequirementHandlerNotFoundException Thrown if there was an error authorizing this request
     */
    #[Delete('/:id'), Authenticate()]
    public function deleteUser(int $id): IResponse
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
    #[Get(''), AuthorizeRoles('admin')]
    public function getPagedUsers(int $pageNumber = 1, int $pageSize = 100): IResponse
    {
        return $this->ok($this->users->getPagedUsers($pageNumber, $pageSize));
    }

    /**
     * Gets a user with the input ID
     *
     * @param int $id The ID of the user to get
     * @return User The user with the input ID
     * @throws UserNotFoundException Thrown if there was no user with the input ID
     */
    #[Get(':id'), Authenticate()]
    public function getUserById(int $id): User
    {
        return $this->users->getUserById($id);
    }
}
