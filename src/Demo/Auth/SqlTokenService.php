<?php

declare(strict_types=1);

namespace App\Demo\Auth;

use PDO;

/**
 * Defines the token service backed by SQL
 */
final class SqlTokenService implements ITokenService
{
    /** @var int The length of generated tokens */
    private const TOKEN_LENGTH = 32;

    /**
     * @param PDO $pdo The PDO instance to use to connect to the database
     */
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @inheritdoc
     */
    public function createToken(int $userId, int $ttlSeconds): string
    {
        $token = \bin2hex(\random_bytes(self::TOKEN_LENGTH));
        $statement = $this->pdo->prepare(<<<SQL
INSERT INTO auth_tokens (user_id, hashed_token, expiration) VALUES (:userId, :hashedToken, :expiration)
SQL
        );
        $statement->execute([
            'userId' => $userId,
            'hashedToken' => self::hashToken($token),
            'expiration' => \time() + $ttlSeconds
        ]);

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function expireToken(int $userId, string $token): void
    {
        $statement = $this->pdo->prepare(<<<SQL
UPDATE auth_tokens SET expiration = :expiration WHERE user_id = :userId AND hashed_token = :hashedToken
SQL
        );
        $statement->execute([
            'expiration' => 0,
            'userId' => $userId,
            'hashToken' => self::hashToken($token)
        ]);
    }

    /**
     * @inheridoc
     */
    public function validateToken(int $userId, string $token): bool
    {
        $statement = $this->pdo->prepare(<<<SQL
SELECT * FROM auth_tokens WHERE user_id = :userId AND hashed_token = :hashedToken AND expiration > :time
SQL
        );
        $statement->execute([
            'userId' => $userId,
            'hashedToken' => self::hashToken($token),
            'time' => time()
        ]);

        return \count($statement->fetchAll()) === 1;
    }

    /**
     * Hashes a token for storage
     *
     * @param string $token The token to hash
     * @return string The hashed token
     */
    private static function hashToken(string $token): string
    {
        return \hash('sha256', $token);
    }
}
