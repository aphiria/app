<?php

declare(strict_types=1);

namespace App\Demo;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Application\IModule;
use Aphiria\Framework\Application\AphiriaComponents;
use Aphiria\Net\Http\HttpStatusCodes;
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
            ->withProblemDetails(
                $appBuilder,
                UserNotFoundException::class,
                null,
                null,
                null,
                HttpStatusCodes::HTTP_NOT_FOUND
            );
    }
}
