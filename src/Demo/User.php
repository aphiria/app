<?php

declare(strict_types=1);

namespace App\Demo;

use Aphiria\Validation\Constraints\Attributes\Email;

/**
 * Defines the user model
 */
class User
{
    /**
     * @param int $id The user ID
     * @param string $email The user email
     */
    public function __construct(public readonly int $id, #[Email] public readonly string $email)
    {
    }
}
