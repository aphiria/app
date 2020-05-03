<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Demo;

/**
 * Defines the interface for user services to implement
 */
interface IUserService
{
    /**
     * Creates many users
     *
     * @param User[] $users The users to create
     * @return User[] The users that were created
     */
    public function createManyUsers(array $users): array;

    /**
     * Creates a user
     *
     * @param User $user The user to create
     * @return User The created user
     */
    public function createUser(User $user): User;

    /**
     * Deletes all users
     */
    public function deleteAllUsers(): void;

    /**
     * Gets all the users
     *
     * @return User[] All the users
     */
    public function getAllUsers(): array;

    /**
     * Gets the number of users
     *
     * @return int The number of users
     */
    public function getNumUsers(): int;

    /**
     * Gets a random user
     *
     * @return User|null The random user, or null if there are no users
     */
    public function getRandomUser(): ?User;

    /**
     * Gets a user by ID
     *
     * @param int $id The ID to search for
     * @return User The user with the corresponding ID
     * @throws UserNotFoundException Thrown if no user with that ID was found
     */
    public function getUserById(int $id): User;
}
