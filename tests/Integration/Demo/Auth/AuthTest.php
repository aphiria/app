<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\Net\Http\Formatting\ResponseHeaderParser;
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
        // TODO: Add an easier way to parse response cookie value in integration tests (probably by including ResponseHeaderParser)
        $logoutResponse = $this->post('/demo/auth/logout', ['Cookie' => (string)(new ResponseHeaderParser())->parseCookies($loginResponse->getHeaders())[0]->value]);
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $logoutResponse);
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
        $this->assertHeaderMatchesRegex('/authToken=;/', $response, 'Set-Cookie');
    }
}
