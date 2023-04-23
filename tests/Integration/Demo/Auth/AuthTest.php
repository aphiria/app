<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use App\Tests\Integration\IntegrationTestCase;

class AuthTest extends IntegrationTestCase
{
    public function testLoggingInWithInvalidCredentialsReturnsUnauthorizedResponse(): void
    {
        // TODO
    }

    public function testLoggingInWithValidCredentialsSetsTokenCookie(): void
    {
        // TODO
    }

    public function testLoggingOutUnsetsTokenCookie(): void
    {
        // TODO
    }
}
