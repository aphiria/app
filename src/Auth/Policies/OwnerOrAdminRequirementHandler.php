<?php

declare(strict_types=1);

namespace App\Auth\Policies;

use Aphiria\Authorization\AuthorizationContext;
use Aphiria\Authorization\IAuthorizationRequirementHandler;
use Aphiria\Security\ClaimType;
use Aphiria\Security\IPrincipal;
use App\Users\User;
use InvalidArgumentException;

/**
 * Defines the requirement handler that checks if a user is the owner or an admin
 *
 * @implements IAuthorizationRequirementHandler<OwnerOrAdminRequirement, User>
 */
final class OwnerOrAdminRequirementHandler implements IAuthorizationRequirementHandler
{
    /**
     * @inheritdoc
     */
    public function handle(IPrincipal $user, object $requirement, AuthorizationContext $authorizationContext): void
    {
        if (!$requirement instanceof OwnerOrAdminRequirement) {
            throw new InvalidArgumentException('Requirement must be of type ' . OwnerOrAdminRequirement::class);
        }

        $userBeingAccessed = $authorizationContext->resource;

        if (!$userBeingAccessed instanceof User) {
            throw new InvalidArgumentException('Resource must be of type ' . User::class);
        }

        if ($userBeingAccessed->id === (int)$user->primaryIdentity?->nameIdentifier) {
            // The user being accessed is the current user
            $authorizationContext->requirementPassed($requirement);

            return;
        }

        foreach ($requirement->authorizedRoles as $authorizedRole) {
            if ($user->hasClaim(ClaimType::Role, $authorizedRole)) {
                // The user is authorized to access the user's account
                $authorizationContext->requirementPassed($requirement);

                return;
            }
        }

        $authorizationContext->fail();
    }
}
