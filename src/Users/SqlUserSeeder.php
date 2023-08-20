<?php

declare(strict_types=1);

namespace App\Users;

use App\Database\IDatabaseSeeder;
use PDO;
use PDOException;

/**
 * Seeds the user service with initial users
 */
final class SqlUserSeeder implements IDatabaseSeeder
{
    /**
     * @param SqlUserService $users The user service to seed
     * @param PDO $pdo The database instance
     */
    public function __construct(
        private readonly SqlUserService $users,
        private readonly PDO $pdo,
        private readonly string $defaultUserEmail,
        private readonly string $defaultUserPassword
    ) {
    }

    /**
     * @inheritdoc
     */
    public function seed(): void
    {
        $this->createTables();

        // Only create the user if they do not exist already
        if ($this->users->getUserByEmail($this->defaultUserEmail) === null) {
            $this->users->createUser(
                new NewUser($this->defaultUserEmail, $this->defaultUserPassword, ['admin']),
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
