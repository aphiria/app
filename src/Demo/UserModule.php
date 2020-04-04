<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Demo;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Application\IModule;
use Aphiria\Framework\Application\AphiriaComponents;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\IHttpResponseMessage;
use Aphiria\Net\Http\IResponseFactory;
use App\Demo\Binders\UserServiceBinder;

/**
 * Defines the example user module
 */
final class UserModule implements IModule
{
    use AphiriaComponents;

    /**
     * @inheritdoc
     */
    public function build(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new UserServiceBinder())
            ->withHttpExceptionResponseFactory(
                $appBuilder,
                UserNotFoundException::class,
                function (UserNotFoundException $ex, IHttpRequestMessage $request, IResponseFactory $responseFactory) {
                    return $responseFactory->createResponse($request, HttpStatusCodes::HTTP_NOT_FOUND);
                }
            );
    }
}
