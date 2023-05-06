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
     * @param int $id The user's ID
     * @param string $email The user's email address
     * @param list<string> $roles The user's roles
     */
    public function __construct(
        public int $id,
        #[Email] public string $email,
        public array $roles
    ) {
    }
}
