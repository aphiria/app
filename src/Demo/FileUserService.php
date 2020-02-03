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
 * Defines the user service
 */
final class FileUserService implements IUserService
{
    /** @var string The path to the file that contains the users */
    private string $filePath;

    /**
     * @param string $filePath The path to the file that contains the users
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @inheritdoc
     */
    public function createManyUsers(array $users): array
    {
        $createdUsers = [];

        foreach ($users as $user) {
            $createdUsers[] = $this->createUser($user);
        }

        return $createdUsers;
    }

    /**
     * @inheritdoc
     */
    public function createUser(User $user): User
    {
        $user->setId(\random_int(0, 1000000));

        if (\file_exists($this->filePath)) {
            $encodedUsers = \json_decode(\file_get_contents($this->filePath), true);
        } else {
            $encodedUsers = [];
        }

        $encodedUsers[] = ['id' => $user->getId(), 'email' => $user->getEmail()];
        \file_put_contents($this->filePath, \json_encode($encodedUsers));

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getAllUsers(): array
    {
        if (!\file_exists($this->filePath)) {
            return [];
        }

        $encodedUsers = \json_decode(\file_get_contents($this->filePath), true);
        $decodedUsers = [];

        foreach ($encodedUsers as $encodedUser) {
            $decodedUsers[] = new User($encodedUser['id'], $encodedUser['email']);
        }

        return $decodedUsers;
    }

    public function getNumUsers(): int
    {
        return \count($this->getAllUsers());
    }

    /**
     * @inheritdoc
     */
    public function getRandomUser(): ?User
    {
        $users = $this->getAllUsers();

        if (\count($users) === 0) {
            return null;
        }

        return $users[\random_int(0, \count($users) - 1)];
    }

    /**
     * @inheritdoc
     */
    public function getUserById(int $id): User
    {
        foreach ($this->getAllUsers() as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }

        throw new UserNotFoundException("No user with ID $id found");
    }
}
