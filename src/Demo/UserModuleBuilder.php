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

use Aphiria\Configuration\IApplicationBuilder;
use Aphiria\Configuration\IModuleBuilder;
use Aphiria\Exceptions\ExceptionResponseFactoryRegistry;
use Aphiria\Net\Http\ContentNegotiation\INegotiatedResponseFactory;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\IHttpResponseMessage;
use App\Demo\Bootstrappers\UserServiceBootstrapper;

/**
 * Defines the example user module builder
 */
final class UserModuleBuilder implements IModuleBuilder
{
    /**
     * @inheritdoc
     */
    public function build(IApplicationBuilder $appBuilder): void
    {
        $appBuilder->withBootstrappers(fn () => [
            new UserServiceBootstrapper
        ]);

        $appBuilder->withComponent(
            'exceptionResponseFactories',
            fn (ExceptionResponseFactoryRegistry $factories) => $factories->registerFactory(
                UserNotFoundException::class,
                fn (UserNotFoundException $ex, ?IHttpRequestMessage $request, INegotiatedResponseFactory $responseFactory): IHttpResponseMessage
                    => $responseFactory->createResponse($request, HttpStatusCodes::HTTP_NOT_FOUND)
            )
        );
    }
}
