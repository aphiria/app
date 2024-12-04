<?php

declare(strict_types=1);

namespace App\Health\Api\Controllers;

use Aphiria\Api\Controllers\Controller;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\IResponse;
use Aphiria\Routing\Attributes\Get;

class HealthController extends Controller
{
    /**
     * Checks the health of the API
     *
     * @return IResponse The OK response
     * @throws HttpException Thrown if the request could not be negotiated
     */
    #[Get('/health')]
    public function checkHealth(): IResponse
    {
        return $this->ok();
    }
}
