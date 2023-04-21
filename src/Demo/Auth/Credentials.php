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
 * Defines user credentials
 */
final readonly class Credentials
{
    /**
     * @param string $email The user email address
     * @param string $password The user password
     */
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
