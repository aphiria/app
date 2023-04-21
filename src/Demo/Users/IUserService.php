<?php

declare(strict_types=1);

namespace App\Demo\Users;

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
     * @return UserViewModel The created user
     */
    public function createUser(NewUser $newUser, bool $allowRoles = false): UserViewModel;

    /**
     * Gets a page of users
     *
     * @param int $pageNumber The page number (0-indexed) to retrieve from
     * @param int $pageSize The size of the page
     * @return UserViewModel The page of users
     * @throws InvalidPageException Thrown if the page number or size was invalid
     */
    public function getPagedUsers(int $pageNumber = 0, int $pageSize = 100): array;

    /**
     * Gets a user with the input credentials
     *
     * @param string $email The email address to look up
     * @param string $password The user password
     * @return UserViewModel|null The user if one was found, otherwise null
     */
    public function getUserByEmailAndPassword(string $email, string $password): ?UserViewModel;

    /**
     * Gets a user by ID
     *
     * @param string $id The ID to search for
     * @return UserViewModel The user with the corresponding ID
     * @throws UserNotFoundException Thrown if no user with that ID was found
     */
    public function getUserById(string $id): UserViewModel;
}
