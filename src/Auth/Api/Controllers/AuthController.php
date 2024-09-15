<?php

declare(strict_types=1);

namespace App\Auth\Api\Controllers;

use Aphiria\Api\Controllers\Controller as BaseController;
use Aphiria\Authentication\Attributes\Authenticate;
use Aphiria\Authentication\IAuthenticator;
use Aphiria\Net\Http\IResponse;
use Aphiria\Net\Http\Response;
use Aphiria\Routing\Attributes\Controller;
use Aphiria\Routing\Attributes\Post;
use Exception;

/**
 * Defines the auth controller
 */
#[Controller('/auth')]
final class AuthController extends BaseController
{
    /**
     * @param IAuthenticator $authenticator The authenticator
     */
    public function __construct(private readonly IAuthenticator $authenticator)
    {
    }

    /**
     * Attempts to log in a user with basic auth and sets an auth token cookie on success
     *
     * @return IResponse The login attempt response
     * @throws Exception Thrown if there was an error with authentication
     */
    #[Post('/login'), Authenticate('basic')]
    public function logIn(): IResponse
    {
        // We authenticate via basic auth, and then log in using cookies for future requests
        $response = new Response();
        /** @psalm-suppress PossiblyNullArgument The user will be set by the basic auth handler */
        $this->authenticator->logIn($this->user, $this->request, $response, 'cookie');

        return $response;
    }

    /**
     * Logs out the user
     *
     * @return IResponse The logout response
     * @throws Exception Thrown if there was an issue with authentication
     */
    #[Post('/logout')]
    public function logOut(): IResponse
    {
        $response = new Response();
        /** @psalm-suppress PossiblyNullArgument The request will be set */
        $this->authenticator->logOut($this->request, $response, 'cookie');

        return $response;
    }
}
