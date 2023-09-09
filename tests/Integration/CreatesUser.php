<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Aphiria\ContentNegotiation\FailedContentNegotiationException;
use Aphiria\ContentNegotiation\MediaTypeFormatters\SerializationException;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\HttpStatusCode;
use App\Users\NewUser;
use App\Users\User;
use Exception;
use RuntimeException;

/**
 * Defines methods for creating users in integration tests
 */
trait CreatesUser
{
    /**
     * Creates a user for use in integration tests
     *
     * @param string $password The user's password
     * @return User The created user
     * @throws FailedContentNegotiationException|SerializationException|HttpException|RuntimeException|Exception Thrown if there was an error creating the user
     */
    private function createUser(string $password = 'password'): User
    {
        // Create a unique email address so we do not have collisions
        $newUser = new NewUser(\bin2hex(\random_bytes(8)) . '@example.com', $password);
        $newUserResponse = $this->post('/users', body: $newUser);

        if ($newUserResponse->getStatusCode() !== HttpStatusCode::Ok) {
            $newUserResponseBody = $newUserResponse->getBody();
            $exceptionMessage = 'Failed to create new user';
            $exceptionMessage .= $newUserResponseBody === null ? '' : ': ' . $newUserResponseBody->readAsString();

            throw new RuntimeException($exceptionMessage);
        }

        /** @var User $createdUser */
        $createdUser = $this->readResponseBodyAs(User::class, $newUserResponse);

        return $createdUser;
    }

}
