<?php

declare(strict_types=1);

namespace App\Demo\Users;

/**
 * Defines the user model that's stored in the database
 */
final readonly class UserEntity
{
    /**
     * @param string $id The user's ID
     * @param string $email The user's email address
     * @param list<string> $roles The user's roles
     * @param string $hashedPassword The user's hashed password
     */
    public function __construct(
        public string $id,
        public string $email,
        public array $roles,
        public string $hashedPassword
    ) {
    }
}