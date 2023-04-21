<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2023 David Young
 * @license   https://github.com/aphiria/app/blob/1.x/LICENSE.md
 */

declare(strict_types=1);

namespace App\Demo\Auth\Api\Controllers;

use Aphiria\Api\Controllers\Controller;
use Aphiria\Authentication\Attributes\Authenticate;
use Aphiria\Authentication\IAuthenticator;
use Aphiria\Net\Http\IResponse;
use Aphiria\Net\Http\Response;
use Aphiria\Routing\Attributes\Post;
use Aphiria\Routing\Attributes\RouteGroup;

/**
 * Defines the auth controller
 */
#[RouteGroup('/demo/auth')]
final class AuthController extends Controller
{
    /**
     * @param IAuthenticator $authenticator The authenticator
     */
    public function __construct(private readonly IAuthenticator $authenticator)
    {
    }

    #[Post('/login'), Authenticate('usernamePassword')]
    public function logIn(): IResponse
    {
        // TODO: Should I just use a basic auth for logging in?
        $response = new Response();
        /*
         * TODO: The below should work, but we're going to try using an attribute to set the user, instead
        $authResult = $this->authenticator->authenticate($this->request, 'usernamePassword');

        if (!$authResult->passed) {
            $this->authenticator->challenge($this->request, $response, 'usernamePassword');

            return $response;
        }

        $this->authenticator->logIn($authResult->user, $this->request, $response, 'token');

        return $response;
        */
        $this->authenticator->logIn($this->getUser(), $this->request, $response, 'token');

        return $response;
    }

    #[Post('/logout')]
    public function logOut(): IResponse
    {
        $response = new Response();
        $this->authenticator->logOut($this->request, $response, 'token');

        return $response;
    }
}
