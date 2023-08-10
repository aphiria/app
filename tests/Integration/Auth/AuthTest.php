<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth;

use Aphiria\Net\Http\HttpStatusCode;
use App\Tests\Integration\CreatesUser;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Integration\SeedsDatabase;

class AuthTest extends IntegrationTestCase
{
    use CreatesUser;
    use SeedsDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
    }

    public function testLoggingInAndOutUnsetsTokenCookie(): void
    {
        $user = $this->createUser(password: 'foo');
        $loginResponse = $this->post(
            '/auth/login',
            ['Authorization' => 'Basic ' . \base64_encode("$user->email:foo")]
        );
        $this->assertHasCookie($loginResponse, 'authToken');
        $logoutResponse = $this->post(
            '/auth/logout',
            ['Cookie' => (string)$this->responseParser->parseCookies($loginResponse)->get('authToken')->value]
        );
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $logoutResponse);
        $this->assertCookieIsUnset($logoutResponse, 'authToken');
    }

    public function testLoggingInWithInvalidCredentialsReturnsUnauthorizedResponse(): void
    {
        $user = $this->createUser(password: 'foo');
        $response = $this->post('/auth/login', ['Authorization' => 'Basic ' . \base64_encode("$user->email:bar")]);
        $this->assertStatusCodeEquals(HttpStatusCode::Unauthorized, $response);
    }

    public function testLoggingInWithValidCredentialsSetsTokenCookie(): void
    {
        $user = $this->createUser(password: 'foo');
        $response = $this->post('/auth/login', ['Authorization' => 'Basic ' . \base64_encode("$user->email:foo")]);
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertHasCookie($response, 'authToken');
    }

    public function testLoggingOutWithoutTokenCookieSetStillUnsetsTokenCookie(): void
    {
        $response = $this->post('/auth/logout');
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertCookieIsUnset($response, 'authToken');
    }
}
