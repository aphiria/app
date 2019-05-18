<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Users\Http\Middleware;

use Aphiria\Middleware\IMiddleware;
use Aphiria\Net\Formatting\UriParser;
use Aphiria\Net\Http\Handlers\IRequestHandler;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\IHttpResponseMessage;
use Aphiria\Net\Http\Response;

/**
 * Defines a dummy authorization middleware for use in tests
 */
class Authorization implements IMiddleware
{
    /**
     * @inheritdoc
     */
    public function handle(IHttpRequestMessage $request, IRequestHandler $next): IHttpResponseMessage
    {
        $uriParser = new UriParser();

        // For the love of god - this is just a proof-of-concept.  Do not use this.
        if (!$uriParser->parseQueryString($request->getUri())->containsKey('letMeIn')) {
            return new Response(HttpStatusCodes::HTTP_UNAUTHORIZED);
        }

        return $next->handle($request);
    }
}
