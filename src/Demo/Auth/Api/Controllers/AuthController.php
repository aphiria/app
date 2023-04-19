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

    #[Post('/login'), Authenticate('basic')]
    public function logIn(): IResponse
    {
        // We authenticate via basic auth, and then log in using cookies for future requests
        $response = new Response();
        $this->authenticator->logIn($this->getUser(), $this->request, $response, 'cookie');

        return $response;
    }

    #[Post('/logout')]
    public function logOut(): IResponse
    {
        $response = new Response();
        $this->authenticator->logOut($this->request, $response, 'cookie');

        return $response;
    }
}
