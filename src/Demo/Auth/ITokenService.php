<?php

declare(strict_types=1);

namespace App\Demo\Auth;

/**
 * Defines the interface for token services to implement
 */
interface ITokenService
{
    /**
     * Creates a token for a user
     *
     * @param int $userId The user ID that the token belongs to
     * @param int $ttlSeconds The number of seconds the token is valid for
     * @return string The token
     */
    public function createToken(int $userId, int $ttlSeconds): string;

    /**
     * Expires the input token
     *
     * @param int $userId The ID of the user whose token we're expiring
     * @param string $token The token to expire
     */
    public function expireToken(int $userId, string $token): void;

    /**
     * Validates a token
     *
     * @param int $userId The ID of the user who the token belongs to
     * @param string $token The token to validate
     * @return bool True if the token is valid for the given user, otherwise false
     */
    public function validateToken(int $userId, string $token): bool;
}
