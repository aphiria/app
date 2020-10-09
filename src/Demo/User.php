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
    public function __construct(private int $id, private string $email)
    {
    }

    /**
     * Gets the user email
     *
     * @return string The email
     */
    #[Email]
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Gets the user ID
     *
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }
}
