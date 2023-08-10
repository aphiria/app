<?php

declare(strict_types=1);

namespace App\Auth\Policies;

/**
 * Defines the requirement for users to delete other users' accounts
 */
final readonly class AuthorizedUserDeleterRequirement
{
    /** @var list<string> The list of roles authorized to delete other users' accounts */
    public array $authorizedRoles;

    /**
     * @param list<string>|string $authorizedRoles The role or list of roles that are authorized to delete other users' accounts
     */
    public function __construct(array|string $authorizedRoles)
    {
        if (!\is_array($authorizedRoles)) {
            $authorizedRoles = [$authorizedRoles];
        }

        $this->authorizedRoles = $authorizedRoles;
    }
}
