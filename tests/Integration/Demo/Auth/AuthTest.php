<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\Net\Http\HttpStatusCode;
use App\Tests\Integration\Demo\Users\CreateUser;
use App\Tests\Integration\IntegrationTestCase;

class AuthTest extends IntegrationTestCase
{
    use CreateUser;

    public function testLoggingInAndOutUnsetsTokenCookie(): void
    {
        $user = $this->createUser(password: 'foo');
        $loginResponse = $this->post('/demo/auth/login', ['Authorization' => 'Basic ' . \base64_encode("$user->email:foo")]);
        $this->assertHasCookie($loginResponse, 'authToken');
        $logoutResponse = $this->post('/demo/auth/logout', ['Cookie' => (string)$this->responseParser->parseCookies($loginResponse)->get('authToken')->value]);
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $logoutResponse);
        // TODO: Add assertion that checks if a cookie was unset
        $this->assertHeaderMatchesRegex('/authToken=;/', $logoutResponse, 'Set-Cookie');
    }

    public function testLoggingInWithInvalidCredentialsReturnsUnauthorizedResponse(): void
    {
        $user = $this->createUser(password: 'foo');
        $response = $this->post('/demo/auth/login', ['Authorization' => 'Basic ' . \base64_encode("$user->email:bar")]);
        $this->assertStatusCodeEquals(HttpStatusCode::Unauthorized, $response);
    }

    public function testLoggingInWithValidCredentialsSetsTokenCookie(): void
    {
        $user = $this->createUser(password: 'foo');
        $response = $this->post('/demo/auth/login', ['Authorization' => 'Basic ' . \base64_encode("$user->email:foo")]);
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        $this->assertHasCookie($response, 'authToken');
    }

    public function testLoggingOutWithoutTokenCookieSetStillUnsetsTokenCookie(): void
    {
        $response = $this->post('/demo/auth/logout');
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $response);
        // TODO: Add assertion that checks if a cookie was unset
        $this->assertHeaderMatchesRegex('/authToken=;/', $response, 'Set-Cookie');
    }
}
