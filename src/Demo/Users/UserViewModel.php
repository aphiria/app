<?php

declare(strict_types=1);

namespace App\Demo\Users;

use Aphiria\Validation\Constraints\Attributes\Email;

/**
 * Defines the user view model
 */
readonly class UserViewModel
{
    /**
     * @param string $id The user ID
     * @param string $email The user email
     * @param list<string> $roles The list of user roles
     */
    public function __construct(
        public string $id,
        #[Email] public string $email,
        public array $roles
    ) {
    }
}
