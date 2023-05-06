<?php

declare(strict_types=1);

namespace App\Demo\Users;

use App\Demo\Database\IDatabaseSeeder;
use PDO;
use PDOException;

/**
 * Seeds the user service with initial users
 */
final class SqlUserSeeder implements IDatabaseSeeder
{
    /** @var string The default user's email address */
    private const DEFAULT_USER_EMAIL = 'admin@example.com';
    /** @var string This is a dummy default user password (in real applications, this should come from an environment variable) */
    private const DEFAULT_USER_PASSWORD = 'abc123';

    /**
     * @param SqlUserService $users The user service to seed
     * @param PDO $pdo The database instance
     */
    public function __construct(private readonly SqlUserService $users, private readonly PDO $pdo)
    {
    }

    /**
     * @inheritdoc
     */
    public function seed(): void
    {
        $this->createTables();

        // Only create the user if they do not exist already
        if ($this->users->getUserByEmail(self::DEFAULT_USER_EMAIL) === null) {
            $this->users->createUser(
                new NewUser(self::DEFAULT_USER_EMAIL, self::DEFAULT_USER_PASSWORD, ['admin']),
                true
            );
        }
    }

    /**
     * Creates the database tables needed to store user data
     *
     * @throws PDOException Thrown if there was an error creating the tables
     */
    private function createTables(): void
    {
        // Create the user table and index
        $this->pdo->exec(
            <<<SQL
CREATE TABLE IF NOT EXISTS users
(id INTEGER PRIMARY KEY AUTOINCREMENT, email TEXT NOT NULL UNIQUE, hashed_password TEXT NOT NULL)
SQL
        );
        $this->pdo->exec(
            <<<SQL
CREATE INDEX IF NOT EXISTS indx_users_email ON users(email)
SQL
        );

        // Create the user role table and index
        $this->pdo->exec(
            <<<SQL
CREATE TABLE IF NOT EXISTS user_roles
(id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, role TEXT NOT NULL, FOREIGN KEY(user_id) REFERENCES users(id))
SQL
        );
        $this->pdo->exec(
            <<<SQL
CREATE INDEX IF NOT EXISTS indx_user_roles_user_id ON user_roles(user_id)
SQL
        );
    }
}
