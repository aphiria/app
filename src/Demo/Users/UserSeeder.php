<?php

declare(strict_types=1);

namespace App\Demo\Users;

/**
 * Seeds the user service with initial users
 */
final class UserSeeder
{
    /** @var string This is a dummy default user password (in real applications, this should come from an environment variable) */
    private const DEFAULT_USER_PASSWORD = 'abc123';

    /**
     * @param IUserService $users The user service to seed
     */
    public function __construct(private readonly IUserService $users)
    {
    }

    /**
     * Seeds the user service
     */
    public function seed(): void
    {
        $this->users->createUser(
            new NewUser('admin@example.com', self::DEFAULT_USER_PASSWORD, ['admin']),
            true
        );
    }
}
