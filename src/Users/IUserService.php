<?php

declare(strict_types=1);

namespace App\Users;

/**
 * Defines the interface for user services to implement
 */
interface IUserService
{
    /**
     * Creates a user
     *
     * @param NewUser $newUser The user to create
     * @param bool $allowRoles Whether we allow the new user to specify their roles (mostly useful with seeding the users with an admin user)
     * @return User The created user
     */
    public function createUser(NewUser $newUser, bool $allowRoles = false): User;

    /**
     * Deletes a user
     *
     * @param int $id The ID of the user to delete
     */
    public function deleteUser(int $id): void;

    /**
     * Gets a page of users
     *
     * @param int $pageNumber The page number (0-indexed) to retrieve from
     * @param int $pageSize The size of the page
     * @return User The page of users
     * @throws InvalidPageException Thrown if the page number or size was invalid
     */
    public function getPagedUsers(int $pageNumber = 1, int $pageSize = 100): array;

    /**
     * Gets a user with the input email address
     *
     * @param string $email The email address to look up
     * @return User|null The user if one was found, otherwise null
     */
    public function getUserByEmail(string $email): ?User;

    /**
     * Gets a user with the input credentials
     *
     * @param string $email The email address to look up
     * @param string $password The user password
     * @return User|null The user if one was found, otherwise null
     */
    public function getUserByEmailAndPassword(string $email, string $password): ?User;

    /**
     * Gets a user by ID
     *
     * @param int $id The ID to search for
     * @return User The user with the corresponding ID
     * @throws UserNotFoundException Thrown if no user with that ID was found
     */
    public function getUserById(int $id): User;
}
