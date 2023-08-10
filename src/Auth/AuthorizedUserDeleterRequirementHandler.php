<?php

declare(strict_types=1);

namespace App\Auth;

use Aphiria\Authorization\AuthorizationContext;
use Aphiria\Authorization\IAuthorizationRequirementHandler;
use Aphiria\Security\ClaimType;
use Aphiria\Security\IPrincipal;
use App\Users\User;
use InvalidArgumentException;

/**
 * Defines the requirement handler that checks if a user is authorized to delete another user's account
 *
 * @implements IAuthorizationRequirementHandler<AuthorizedUserDeleterRequirement, User>
 */
final class AuthorizedUserDeleterRequirementHandler implements IAuthorizationRequirementHandler
{
    /**
     * @inheritdoc
     */
    public function handle(IPrincipal $user, object $requirement, AuthorizationContext $authorizationContext): void
    {
        if (!$requirement instanceof AuthorizedUserDeleterRequirement) {
            throw new InvalidArgumentException('Requirement must be of type ' . AuthorizedUserDeleterRequirement::class);
        }

        $userToDelete = $authorizationContext->resource;

        if (!$userToDelete instanceof User) {
            throw new InvalidArgumentException('Resource must be of type ' . User::class);
        }

        if ($userToDelete->id === (int)$user->getPrimaryIdentity()?->getNameIdentifier()) {
            // The user being deleted is the current user
            $authorizationContext->requirementPassed($requirement);

            return;
        }

        foreach ($requirement->authorizedRoles as $authorizedRole) {
            if ($user->hasClaim(ClaimType::Role, $authorizedRole)) {
                // The user is authorized to delete the user's account
                $authorizationContext->requirementPassed($requirement);

                return;
            }
        }

        $authorizationContext->fail();
    }
}