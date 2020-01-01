<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Users;

/**
 * Defines the user model
 */
class User
{
    /** @var int The user ID */
    private int $id;
    /** @var string The user email */
    private string $email;

    /**
     * @param int $id The user ID
     * @param string $email The user email
     */
    public function __construct(int $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * Gets the user email
     *
     * @return string The email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Gets the user ID
     *
     * @return int The ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the user ID
     *
     * @param int $id The new ID
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
