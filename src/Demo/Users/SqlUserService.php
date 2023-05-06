<?php

declare(strict_types=1);

namespace App\Demo\Users;

use PDO;

/**
 * Defines the user service backed by SQLite
 */
final class SqlUserService implements IUserService
{
    /** @var int The max allowed page size when paging through users */
    private const MAX_PAGE_SIZE = 100;

    /**
     * @param PDO $pdo The PDO instance to use to connect to the database
     */
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @inheritdoc
     */
    public function createUser(NewUser $newUser, bool $allowRoles = false): User
    {
        if (!$allowRoles) {
            // Remove the user's roles
            $newUser = new NewUser($newUser->email, $newUser->password);
        }

        $normalizedEmail = self::normalizeEmail($newUser->email);

        $this->pdo->beginTransaction();
        // Insert the user
        $createUserStatement = $this->pdo->prepare(
            <<<SQL
INSERT INTO users (email, hashed_password) VALUES (:email, :hashedPassword)
SQL
        );
        $createUserStatement->execute([
            'email' => $normalizedEmail,
            'hashedPassword' => self::hashPassword($newUser->password)
        ]);
        $createdUser = new User(
            (int)$this->pdo->lastInsertId(),
            $normalizedEmail,
            $newUser->roles
        );

        // Insert the roles
        foreach ($newUser->roles as $role) {
            $createRoleStatement = $this->pdo->prepare(
                <<<SQL
INSERT INTO user_roles (user_id, role) VALUES (:userId, :role)
SQL
            );
            $createRoleStatement->execute([
                'userId' => $createdUser->id,
                'role' => $role
            ]);
        }

        $this->pdo->commit();

        return $createdUser;
    }

    /**
     * @inheritdoc
     */
    public function deleteUser(int $id): void
    {
        $this->pdo->beginTransaction();

        // Delete the user roles
        $deleteRolesStatement = $this->pdo->prepare(
            <<<SQL
DELETE FROM user_roles WHERE user_id = :userId
SQL
        );
        $deleteRolesStatement->execute(['userId' => $id]);

        // Delete the user
        $deleteUserStatement = $this->pdo->prepare(
            <<<SQL
DELETE FROM users WHERE id = :userId
SQL
        );
        $deleteUserStatement->execute(['userId' => $id]);

        $this->pdo->commit();
    }

    /**
     * @inheritdoc
     */
    public function getPagedUsers(int $pageNumber = 1, int $pageSize = 100): array
    {
        if ($pageNumber < 0) {
            throw new InvalidPageException('Page number must begin at 0');
        }

        if ($pageSize > self::MAX_PAGE_SIZE) {
            throw new InvalidPageException('Page size cannot exceed ' . self::MAX_PAGE_SIZE);
        }

        $statement = $this->pdo->prepare(
            <<<SQL
SELECT users.id, users.email, GROUP_CONCAT(user_roles.role) AS roles FROM users
LEFT JOIN user_roles ON user_roles.user_id = users.id
GROUP BY users.id, users.email
ORDER BY users.id ASC
LIMIT :start,:limit
SQL
        );
        $statement->execute(['start' => ($pageNumber - 1) * $pageSize, 'limit' => $pageSize]);
        $users = [];

        /** @var array{id: int, email: string, roles: string} $row */
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $users[] = self::createUserFromRow($row);
        }

        return $users;
    }

    /**
     * @inheritdoc
     */
    public function getUserByEmail(string $email): ?User
    {
        $rows = $this->queryUserByEmail($email);

        if (\count($rows) !== 1) {
            return null;
        }

        return self::createUserFromRow($rows[0]);
    }

    /**
     * @inheritdoc
     */
    public function getUserByEmailAndPassword(string $email, string $password): ?User
    {
        $rows = $this->queryUserByEmail($email);

        if (\count($rows) !== 1 || !\password_verify($password, $rows[0]['hashed_password'])) {
            return null;
        }

        return self::createUserFromRow($rows[0]);
    }

    /**
     * @inheritdoc
     */
    public function getUserById(int $id): User
    {
        $statement = $this->pdo->prepare(
            <<<SQL
SELECT users.id, users.email, GROUP_CONCAT(user_roles.role) AS roles FROM users
LEFT JOIN user_roles ON user_roles.user_id = users.id
WHERE users.id = :id
GROUP BY users.id, users.email
SQL
        );
        $statement->execute(['id' => $id]);
        /** @var array{id: int, email: string, roles: string} $row */
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (empty($row)) {
            throw new UserNotFoundException("No user found with ID $id");
        }

        return self::createUserFromRow($row);
    }

    /**
     * Creates a user from an entity
     *
     * @param array{id: int, email: string, roles: ?string, hashed_password?: string} $userRow The row of user data to create the user from
     * @return User The created user
     */
    private static function createUserFromRow(array $userRow): User
    {
        $roles = $userRow['roles'] === null ? [] : \explode(',', $userRow['roles']);

        return new User(
            (int)$userRow['id'],
            (string)$userRow['email'],
            $roles
        );
    }

    /**
     * Hashes a password for storage
     *
     * @param string $password The password to hash
     * @return string The hashed password
     */
    private static function hashPassword(string $password): string
    {
        return \password_hash($password, PASSWORD_ARGON2ID);
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
     * Queries for a user by email address
     *
     * @param string $email The email address to query for
     * @return list<array{id: int, email: string, roles: ?string, hashed_password: string}> The row of data for the user
     */
    private function queryUserByEmail(string $email): array
    {
        $statement = $this->pdo->prepare(
            <<<SQL
SELECT users.id, users.email, GROUP_CONCAT(user_roles.role) AS roles, users.hashed_password FROM users
LEFT JOIN user_roles ON user_roles.user_id = users.id
WHERE users.email = :email
GROUP BY users.id, users.email, users.hashed_password
SQL
        );
        $statement->execute(['email' => self::normalizeEmail($email)]);
        /** @var list<array{id: int, email: string, roles: string, hashed_password: string}> $rows */
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }
}
