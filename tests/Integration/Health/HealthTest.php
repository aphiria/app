<?php

declare(strict_types=1);

namespace App\Tests\Integration\Health;

use Aphiria\Net\Http\HttpStatusCode;
use App\Tests\Integration\IntegrationTestCase;

class HealthTest extends IntegrationTestCase
{
    public function testCheckingHealthReturnsOk(): void
    {
        $this->assertStatusCodeEquals(HttpStatusCode::Ok, $this->get('/health'));
    }
}
