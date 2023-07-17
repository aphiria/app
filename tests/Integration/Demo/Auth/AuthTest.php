<?php

declare(strict_types=1);

namespace App\Tests\Integration\Demo\Auth;

use Aphiria\DependencyInjection\Container;
use Aphiria\Net\Http\HttpStatusCode;
use App\Tests\Integration\Demo\Authenticates;
use App\Tests\Integration\Demo\CreatesUser;
use App\Tests\Integration\Demo\SeedsDatabase;
use App\Tests\Integration\IntegrationTestCase;
use RuntimeException;

class AuthTest extends IntegrationTestCase
{
    use Authenticates;
    use CreatesUser;
    use SeedsDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (($container = Container::$globalInstance) === null) {
            throw new RuntimeException('No global container instance set');
        }

        // TODO: Where should this live once the PoC is done?
        $this->seed($container);
        $this->createTestingAuthenticator($container);
    }

    public function testLoggingInAndOutUnsetsTokenCookie(): void
    {
        $user = $this->createUser(password: 'foo');
        $loginResponse = $this->post('/demo/auth/login', ['Authorization' => 'Basic ' . \base64_encode("$user->email:foo")]);
        $this->assertHasCookie($loginResponse, 'authToken');
        $logoutResponse = $this->post('/demo/auth/logout', ['Cookie' => (string)$this->responseParser->parseCookies($loginResponse)->get('authToken')->value]);
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $logoutResponse);
        $this->assertCookieIsUnset($logoutResponse, 'authToken');
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
        $this->assertCookieIsUnset($response, 'authToken');
    }
}
