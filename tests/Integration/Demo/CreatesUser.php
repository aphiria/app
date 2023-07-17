<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo;

use Aphiria\ContentNegotiation\FailedContentNegotiationException;
use Aphiria\ContentNegotiation\MediaTypeFormatters\SerializationException;
use Aphiria\Net\Http\HttpException;
use App\Demo\Users\NewUser;
use App\Demo\Users\User;
use Exception;

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
     * @throws FailedContentNegotiationException|SerializationException|HttpException|Exception Thrown if there was an error creating the user
     */
    private function createUser(string $password = 'password'): User
    {
        // Create a unique email address so we do not have collisions
        $newUser = new NewUser(\bin2hex(\random_bytes(8)) . '@example.com', $password);
        /** @var User $createdUser */
        $createdUser = $this->readResponseBodyAs(User::class, $this->post('/demo/users', body: $newUser));

        return $createdUser;
    }

}
