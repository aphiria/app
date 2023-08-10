<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Aphiria\ContentNegotiation\FailedContentNegotiationException;
use Aphiria\ContentNegotiation\MediaTypeFormatters\SerializationException;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\HttpStatusCode;
use Aphiria\Security\PrincipalBuilder;
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
     * @param bool $createAsAdmin Whether we create this user as the admin
     * @param string $password The user's password
     * @param string|list<string> $roles The user's roles
     * @return User The created user
     * @throws FailedContentNegotiationException|SerializationException|HttpException|RuntimeException|Exception Thrown if there was an error creating the user
     */
    private function createUser(bool $createAsAdmin = false, string $password = 'password', string|array $roles = []): User
    {
        // Create a unique email address so we do not have collisions
        $roles = \is_string($roles) ? [$roles] : $roles;
        $newUser = new NewUser(\bin2hex(\random_bytes(8)) . '@example.com', $password, $roles);
        $actingAs = $createAsAdmin
            ? (new PrincipalBuilder('example.com'))->withRoles('admin')
                ->build()
            : (new PrincipalBuilder('example.com'))->build();

        $newUserResponse = $this->actingAs($actingAs, fn () => $this->post('/users', body: $newUser));

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
