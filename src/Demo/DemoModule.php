<?php

declare(strict_types=1);

namespace App\Demo;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Application\IModule;
use Aphiria\Framework\Application\AphiriaComponents;
use Aphiria\Net\Http\HttpStatusCode;
use App\Demo\Binders\UserServiceBinder;

/**
 * Defines the demo module
 */
final class DemoModule implements IModule
{
    use AphiriaComponents;

    /**
     * @inheritdoc
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinders($appBuilder, new UserServiceBinder())
            ->withProblemDetails(
                $appBuilder,
                UserNotFoundException::class,
                status: HttpStatusCode::NotFound
            );
    }
}
