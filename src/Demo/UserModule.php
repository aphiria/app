<?php

declare(strict_types=1);

namespace App\Demo;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Application\IModule;
use Aphiria\Framework\Application\AphiriaComponents;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IRequest;
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
                static function (UserNotFoundException $ex, IRequest $request, IResponseFactory $responseFactory) {
                    return $responseFactory->createResponse($request, HttpStatusCodes::HTTP_NOT_FOUND);
                }
            );
    }
}
