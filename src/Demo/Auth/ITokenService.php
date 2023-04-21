<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2023 David Young
 * @license   https://github.com/aphiria/app/blob/1.x/LICENSE.md
 */

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
     * @param string $userId The user ID that the token belongs to
     * @return string The token
     */
    public function createToken(string $userId): string;

    /**
     * Expires the input token
     *
     * @param string $token The token to expire
     */
    public function expireToken(string $token): void;

    /**
     * Gets the user ID with the input token
     *
     * @param string $token The token to search for
     * @return string|null The user ID if one was found, otherwise null
     */
    public function getUserIdFromToken(string $token): ?string;
}
