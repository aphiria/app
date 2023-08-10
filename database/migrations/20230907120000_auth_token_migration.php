<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Defines the auth token migration
 */
class AuthTokenMigration extends AbstractMigration
{
    /**
     * Creates the tables necessary for auth tokens
     */
    public function change(): void
    {
        $this->table('auth_tokens')
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('hashed_token', 'text', ['null' => false])
            ->addColumn('expiration', 'integer', ['null' => false])
            ->addForeignKey('user_id', 'users', 'id')
            ->addIndex(['user_id', 'hashed_token'])
            ->create();
    }
}
