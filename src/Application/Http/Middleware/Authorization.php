<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace App\Application\Http\Middleware;

use Opulence\Api\Middleware\IMiddleware;
use Opulence\Net\Formatting\UriParser;
use Opulence\Net\Http\Handlers\IRequestHandler;
use Opulence\Net\Http\HttpStatusCodes;
use Opulence\Net\Http\IHttpRequestMessage;
use Opulence\Net\Http\IHttpResponseMessage;
use Opulence\Net\Http\Response;

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