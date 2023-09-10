<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Defines the user seeder
 */
class UserSeeder extends AbstractSeed
{
    /**
     * @inheritdoc
     */
    public function run(): void
    {
        // Create the default user if they do not already exist
        /** @var PDOStatement $queryForDefaultUser */
        $queryForDefaultUser = $this->query(
            'SELECT * FROM users WHERE email = :email',
            ['email' => \getenv('USER_DEFAULT_EMAIL')]
        );

        if (!empty($queryForDefaultUser->fetchAll())) {
            $this->output->writeln('<info>Default user already exists, skipping seeding...</info>');

            return;
        }

        $this->output->writeln('<info>Inserting default user</info>');
        $this->insert(
            'users',
            [
                'email' => (string)\getenv('USER_DEFAULT_EMAIL'),
                'hashed_password' => \password_hash((string)\getenv('USER_DEFAULT_PASSWORD'), PASSWORD_ARGON2ID)
            ]
        );
        $this->insert(
            'user_roles',
            [
                'user_id' => (int)$this->getAdapter()->getConnection()->lastInsertId(),
                'role' => 'admin'
            ]
        );
    }
}
