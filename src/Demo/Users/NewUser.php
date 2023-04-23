<?php

declare(strict_types=1);

namespace App\Demo\Users;

use Aphiria\Validation\Constraints\Attributes\Email;

/**
 * Defines a new user
 */
final readonly class NewUser
{
    /**
     * @param string $email The user's email address
     * @param string $password The user's unhashed password
     * @param list<string> $roles The user's roles
     */
    public function __construct(
        #[Email] public string $email,
        public string $password,
        public array $roles = []
    ) {
    }
}
