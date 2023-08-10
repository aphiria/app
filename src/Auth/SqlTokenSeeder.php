<?php

declare(strict_types=1);

namespace App\Auth;

use App\Database\IDatabaseSeeder;
use PDO;

/**
 * Seeds the token database
 */
final class SqlTokenSeeder implements IDatabaseSeeder
{
    /**
     * @param PDO $pdo The database instance
     */
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @inheritdoc
     */
    public function seed(): void
    {
        // Create the auth token table and indices
        $this->pdo->exec(
            <<<SQL
CREATE TABLE IF NOT EXISTS auth_tokens
(id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, hashed_token TEXT NOT NULL, expiration INTEGER NOT NULL, FOREIGN KEY(user_id) REFERENCES users(id))
SQL
        );
        $this->pdo->exec(
            <<<SQL
CREATE INDEX IF NOT EXISTS indx_auth_tokens_user_id ON auth_tokens(user_id)
SQL
        );
        $this->pdo->exec(
            <<<SQL
CREATE INDEX IF NOT EXISTS indx_auth_tokens_hashed_token ON auth_tokens(hashed_token)
SQL
        );
    }
}
