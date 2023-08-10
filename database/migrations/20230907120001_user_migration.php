<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Defines the user migration
 */
class UserMigration extends AbstractMigration
{
    /**
     * Creates the tables necessary for users and roles
     */
    public function change(): void
    {
        $this->table('users')
            ->addColumn('email', 'text', ['null' => false])
            ->addColumn('hashed_password', 'text', ['null' => false])
            ->addIndex('email', ['unique' => true])
            ->create();

        $this->table('user_roles')
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => false])
            ->addForeignKey('user_id', 'users', 'id')
            ->create();
    }
}
