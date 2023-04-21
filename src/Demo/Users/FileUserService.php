<?php

declare(strict_types=1);

namespace App\Demo\Users;

use Exception;
use JsonException;

/**
 * Defines the user service backed by file storage
 */
final class FileUserService implements IUserService
{
    /** @var int The max allowed page size when paging through users */
    private const MAX_PAGE_SIZE = 100;

    /**
     * @param string $filePath The path to the file that contains the users
     */
    public function __construct(private readonly string $filePath)
    {
    }

    /**
     * @inheritdoc
     */
    public function createUser(NewUser $newUser, bool $allowRoles = false): UserViewModel
    {
        if (!$allowRoles) {
            // Remove the user's roles
            $newUser = new NewUser($newUser->email, $newUser->password);
        }

        $userEntities = [];
        $pageNumber = 0;

        while (\count($pagedUserEntities = $this->readUsersFromFile($pageNumber, self::MAX_PAGE_SIZE)) > 0) {
            $userEntities = [...$userEntities, ...$pagedUserEntities];
        }

        $newUserEntity = self::createUserEntityFromNewUser($newUser);
        $userEntities[] = $newUserEntity;
        $this->writeUsersToFile($userEntities);

        return self::createUserFromUserEntity($newUserEntity);
    }

    /**
     * @inheritdoc
     */
    public function getPagedUsers(int $pageNumber = 0, int $pageSize = 100): array
    {
        if ($pageNumber < 0) {
            throw new InvalidPageException('Page number must begin at 0');
        }

        if ($pageSize > self::MAX_PAGE_SIZE) {
            throw new InvalidPageException('Page size cannot exceed ' . self::MAX_PAGE_SIZE);
        }

        $users = [];

        foreach ($this->readUsersFromFile($pageNumber, $pageSize) as $userEntity) {
            $users[] = self::createUserFromUserEntity($userEntity);
        }

        return $users;
    }

    /**
     * @inheritdoc
     */
    public function getUserByEmailAndPassword(string $email, string $password): ?UserViewModel
    {
        $normalizedEmail = self::normalizeEmail($email);
        $pageNumber = 0;

        while (\count($pagedUserEntities = $this->readUsersFromFile($pageNumber, self::MAX_PAGE_SIZE)) > 0) {
            foreach ($pagedUserEntities as $userEntity) {
                if ($userEntity->email === $normalizedEmail) {
                    if (!\password_verify($password, $userEntity->hashedPassword)) {
                        // No point in proceeding if the password was incorrect
                        return null;
                    }

                    return self::createUserFromUserEntity($userEntity);
                }
            }

            $pageNumber++;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUserById(string $id): UserViewModel
    {
        $pageNumber = 0;

        while (\count($pagedUserEntities = $this->readUsersFromFile($pageNumber, self::MAX_PAGE_SIZE)) > 0) {
            foreach ($pagedUserEntities as $userEntity) {
                if ($userEntity->id === $id) {
                    return self::createUserFromUserEntity($userEntity);
                }
            }

            $pageNumber++;
        }

        throw new UserNotFoundException("No user with ID $id found");
    }

    /**
     * Creates a user entity from a new user
     *
     * @param NewUser $user The new user
     * @return UserEntity The created user entity
     * @throws Exception Thrown if there was an error creating the user entity
     */
    private static function createUserEntityFromNewUser(NewUser $user): UserEntity
    {
        // Create a GUID
        $randomBytes = \random_bytes(16);
        $randomBytes[6] = \chr(\ord($randomBytes[6]) & 0x0f | 0x40);
        $randomBytes[8] = \chr(\ord($randomBytes[8]) & 0x3f | 0x80);
        $id = \vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($randomBytes), 4));

        return new UserEntity(
            $id,
            self::normalizeEmail($user->email),
            [],
            \password_hash($user->password, PASSWORD_ARGON2ID)
        );
    }

    /**
     * Creates a user from an entity
     *
     * @param UserEntity $userEntity The user entity to create a user from
     * @return UserViewModel The created user
     */
    private static function createUserFromUserEntity(UserEntity $userEntity): UserViewModel
    {
        return new UserViewModel($userEntity->id, $userEntity->email, $userEntity->roles);
    }

    /**
     * Normalizes the email address so that casing and string padding do not affect lookups
     *
     * @param string $email The email address to normalize
     * @return string The normalized email address
     */
    private static function normalizeEmail(string $email): string
    {
        return \strtolower(\trim($email));
    }

    /**
     * Reads the users from the local file
     *
     * @param int $pageNumber The page to retrieve
     * @param int $pageSize The size of the page to retrieve
     * @return list<UserEntity> The list of users
     */
    private function readUsersFromFile(int $pageNumber, int $pageSize): array
    {
        if (!\file_exists($this->filePath)) {
            return [];
        }

        /** @var array<array{id: string, email: string, roles: list<string>, hashedPassword: string}>|false $encodedUserEntities */
        $encodedUserEntities = \json_decode(\file_get_contents($this->filePath), true);

        if (!\is_array($encodedUserEntities)) {
            return [];
        }

        $pagedEncodedUserEntities = \array_slice($encodedUserEntities, $pageNumber, $pageSize);
        $decodedUserEntities = [];

        foreach ($pagedEncodedUserEntities as $encodedUser) {
            $decodedUserEntities[] = new UserEntity(
                $encodedUser['id'],
                $encodedUser['email'],
                $encodedUser['roles'],
                $encodedUser['hashedPassword']
            );
        }

        return $decodedUserEntities;
    }

    /**
     * Writes the users back to the file
     *
     * @param list<UserEntity> $userEntities The users to write
     * @throws JsonException Thrown if there was an error encoding the JSON
     */
    private function writeUsersToFile(array $userEntities): void
    {
        $encodedUserEntities = [];

        foreach ($userEntities as $userEntity) {
            $encodedUserEntities[] = [
                'id' => $userEntity->id,
                'email' => $userEntity->email,
                'roles' => $userEntity->roles,
                'hashedPassword' => $userEntity->hashedPassword
            ];
        }

        \file_put_contents($this->filePath, \json_encode($encodedUserEntities, JSON_THROW_ON_ERROR));
    }
}
