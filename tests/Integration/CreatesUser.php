<?php

declare(strict_types=1);

namespace App\Tests\Integration;

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
     * @throws Exception Thrown if there was an error creating the user
     */
    private function createUser(bool $createAsAdmin = false, string $password = 'password', string|array $roles = []): User
    {
        // Create a unique email address so we do not have collisions
        $roles = \is_string($roles) ? [$roles] : $roles;
        $newUser = new NewUser(\bin2hex(\random_bytes(8)) . '@example.com', $password, $roles);
        $principalBuilder = (new PrincipalBuilder('example.com'))->withNameIdentifier(0);
        $actingAs = $createAsAdmin
            ? $principalBuilder->withRoles('admin')
                ->build()
            : $principalBuilder->build();
        $createUserResponse = $this->actingAs($actingAs, fn () => $this->post('/users', body: $newUser));

        if ($createUserResponse->getStatusCode() !== HttpStatusCode::Ok) {
            $newUserResponseBody = $createUserResponse->getBody();
            $exceptionMessage = 'Failed to create new user';
            $exceptionMessage .= $newUserResponseBody === null ? '' : ': ' . $newUserResponseBody->readAsString();

            throw new RuntimeException($exceptionMessage);
        }

        /** @var User */
        return $this->readResponseBodyAs(User::class, $createUserResponse);
    }
}
