<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Framework\Api\Binders\ControllerBinder;
use Aphiria\Framework\Application\AphiriaComponents;
use Aphiria\Framework\Console\Binders\CommandBinder;
use Aphiria\Framework\Net\Binders\ContentNegotiatorBinder;
use Aphiria\Framework\Routing\Binders\RoutingBinder;
use Aphiria\Framework\Serialization\Binders\SerializerBinder;
use Aphiria\Framework\Validation\Binders\ValidationBinder;
use App\Demo\UserModule;

/**
 * Defines the application configuration
 */
final class App
{
    use AphiriaComponents;

    /** @var IApplicationBuilder The app builder to use when configuring the application */
    private IApplicationBuilder $appBuilder;
    /** @var IContainer The DI container that can resolve dependencies */
    private IContainer $container;

    /**
     * @param IApplicationBuilder $appBuilder The app builder to use when configuring the application
     * @param IContainer $container The DI container that can resolve dependencies
     */
    public function __construct(IApplicationBuilder $appBuilder, IContainer $container)
    {
        $this->appBuilder = $appBuilder;
        $this->container = $container;
    }

    /**
     * Configures the application's modules
     */
    public function configure(): void
    {
        $this->withExceptionHandlerMiddleware($this->appBuilder)
            ->withRouteAnnotations($this->appBuilder)
            ->withValidatorAnnotations($this->appBuilder)
            ->withCommandAnnotations($this->appBuilder)
            ->withBinders($this->appBuilder, [
                new SerializerBinder(),
                new ValidationBinder(),
                new ContentNegotiatorBinder(),
                new ControllerBinder(),
                new RoutingBinder(),
                new CommandBinder()
            ]);

        // Register any modules
        $this->appBuilder->withModule(new UserModule($this->container));
    }
}
