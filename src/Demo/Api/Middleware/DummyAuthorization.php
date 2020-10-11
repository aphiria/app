<?php

declare(strict_types=1);

namespace App\Demo\Api\Middleware;

use Aphiria\Middleware\IMiddleware;
use Aphiria\Net\Formatting\UriParser;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IRequest;
use Aphiria\Net\Http\IRequestHandler;
use Aphiria\Net\Http\IResponse;
use Aphiria\Net\Http\Response;

/**
 * Defines a dummy authorization middleware for use in tests
 */
final class DummyAuthorization implements IMiddleware
{
    /**
     * @inheritdoc
     */
    public function handle(IRequest $request, IRequestHandler $next): IResponse
    {
        $uriParser = new UriParser();

        // This is just a proof-of-concept - do not use this!
        if (!$uriParser->parseQueryString($request->getUri())->containsKey('letMeIn')) {
            return new Response(HttpStatusCodes::UNAUTHORIZED);
        }

        return $next->handle($request);
    }
}
