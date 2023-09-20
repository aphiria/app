<?php

declare(strict_types=1);

namespace App\Users\Api\Controllers;

use Aphiria\Api\Controllers\Controller as BaseController;
use Aphiria\Authentication\Attributes\Authenticate;
use Aphiria\Authentication\IAuthenticator;
use Aphiria\Authorization\Attributes\AuthorizeRoles;
use Aphiria\Authorization\IAuthority;
use Aphiria\Authorization\PolicyNotFoundException;
use Aphiria\Authorization\RequirementHandlerNotFoundException;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\IResponse;
use Aphiria\Routing\Attributes\Controller;
use Aphiria\Routing\Attributes\Delete;
use Aphiria\Routing\Attributes\Get;
use Aphiria\Routing\Attributes\Post;
use App\Users\InvalidPageException;
use App\Users\IUserService;
use App\Users\NewUser;
use App\Users\User;
use App\Users\UserNotFoundException;
use Exception;

/**
 * Defines the user controller
 */
#[Controller('/users')]
final class UserController extends BaseController
{
    /**
     * @param IUserService $users The user service
     * @param IAuthenticator $authenticator The authenticator
     * @param IAuthority $authority The authority
     */
    public function __construct(
        private readonly IUserService $users,
        private readonly IAuthenticator $authenticator,
        private readonly IAuthority $authority
    ) {
    }

    /**
     * Creates a user
     *
     * @param NewUser $user The user to create
     * @return User The created user
     * @throws Exception Thrown if there was an error authenticating or authorizing this request
     */
    #[Post('')]
    public function createUser(NewUser $user): User
    {
        $canGrantRoles = false;
        /** @psalm-suppress PossiblyNullArgument The user will be set */
        $authenticationResult = $this->authenticator->authenticate($this->request, 'cookie');

        if ($authenticationResult->passed) {
            /** @psalm-suppress PossiblyNullArgument The user will be set */
            $canGrantRoles = $this->authority->authorize($authenticationResult->user, 'authorized-user-role-granter')->passed;
        }

        return $this->users->createUser($user, $canGrantRoles);
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
            // To hide prevent iterating over our users, we'll just return a 403
            return $this->forbidden();
        }

        /** @psalm-suppress PossiblyNullArgument The user will be set */
        if (!$this->authority->authorize($this->getUser(), 'owner-or-admin', $userToDelete)->passed) {
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
     * @return IResponse The response with the user
     * @throws UserNotFoundException Thrown if there was no user with the input ID
     * @throws Exception Thrown if there was an error authenticating or authorizing the user
     */
    #[Get(':id'), Authenticate()]
    public function getUserById(int $id): IResponse
    {
        try {
            $userToGet = $this->users->getUserById($id);
        } catch (UserNotFoundException) {
            // To hide prevent iterating over our users, we'll just return a 403
            return $this->forbidden();
        }

        /** @psalm-suppress PossiblyNullArgument The user will be set */
        if (!$this->authority->authorize($this->getUser(), 'owner-or-admin', $userToGet)->passed) {
            return $this->forbidden();
        }

        return $this->ok($this->users->getUserById($id));
    }
}
