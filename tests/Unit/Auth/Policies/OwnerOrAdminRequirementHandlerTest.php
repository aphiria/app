<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Policies;

use Aphiria\Authorization\AuthorizationContext;
use Aphiria\Security\PrincipalBuilder;
use App\Auth\Policies\OwnerOrAdminRequirement;
use App\Auth\Policies\OwnerOrAdminRequirementHandler;
use App\Users\User;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OwnerOrAdminRequirementHandlerTest extends TestCase
{
    private OwnerOrAdminRequirementHandler $requirementHandler;

    protected function setUp(): void
    {
        $this->requirementHandler = new OwnerOrAdminRequirementHandler();
    }

    public function testBeingNeitherAdminNorOwnerFails(): void
    {
        $userToAccess = new User(1, 'foo@example.com', []);
        $userAccessing = (new PrincipalBuilder('example.com'))->withNameIdentifier(2)
            ->build();
        $requirement = new OwnerOrAdminRequirement('admin');
        $context = new AuthorizationContext($userAccessing, [$requirement], $userToAccess);
        $this->requirementHandler->handle($userAccessing, $requirement, $context);
        $this->assertFalse($context->allRequirementsPassed);
    }

    public function testBeingOwnerPasses(): void
    {
        $userToAccess = new User(1, 'foo@example.com', []);
        $userAccessing = (new PrincipalBuilder('example.com'))->withNameIdentifier(1)
            ->build();
        $requirement = new OwnerOrAdminRequirement('admin');
        $context = new AuthorizationContext($userAccessing, [$requirement], $userToAccess);
        $this->requirementHandler->handle($userAccessing, $requirement, $context);
        $this->assertTrue($context->allRequirementsPassed);
    }

    public function testHavingAnAdminRolePasses(): void
    {
        $userToAccess = new User(1, 'foo@example.com', []);
        $userAccessing = (new PrincipalBuilder('example.com'))->withRoles('admin')
            ->build();
        $requirement = new OwnerOrAdminRequirement('admin');
        $context = new AuthorizationContext($userAccessing, [$requirement], $userToAccess);
        $this->requirementHandler->handle($userAccessing, $requirement, $context);
        $this->assertTrue($context->allRequirementsPassed);
    }

    public function testRequirementOfIncorrectTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Requirement must be of type ' . OwnerOrAdminRequirement::class);
        $userToAccess = new User(1, 'foo@example.com', []);
        $userAccessing = (new PrincipalBuilder('example.com'))->withRoles('admin')
            ->build();
        $requirement = $this;
        $context = new AuthorizationContext($userAccessing, [$requirement], $userToAccess);
        /** @psalm-suppress InvalidArgument Purposely testing this */
        $this->requirementHandler->handle($userAccessing, $requirement, $context);
    }

    public function testResourceOfIncorrectTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource must be of type ' . User::class);
        $userAccessing = (new PrincipalBuilder('example.com'))->build();
        $requirement = new OwnerOrAdminRequirement('admin');
        $context = new AuthorizationContext($userAccessing, [$requirement], $this);
        /** @psalm-suppress InvalidArgument Purposely testing this */
        $this->requirementHandler->handle($userAccessing, $requirement, $context);
    }
}
