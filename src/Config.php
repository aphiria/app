<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App;

use Aphiria\Configuration\AphiriaComponentBuilder;
use Aphiria\Configuration\IApplicationBuilder;
use Aphiria\DependencyInjection\IContainer;
use App\Api\Bootstrappers\ContentNegotiatorBootstrapper;
use App\Api\Bootstrappers\DependencyInjectionBootstrapper;
use App\Api\Bootstrappers\ExceptionHandlerBootstrapper;
use App\Api\Bootstrappers\RoutingBootstrapper;
use App\Api\Bootstrappers\SerializerBootstrapper;
use App\Api\Bootstrappers\ValidationBootstrapper;
use App\Console\Bootstrappers\CommandBootstrapper;
use App\Logging\Bootstrappers\LoggerBootstrapper;
use App\Users\UserModuleBuilder;

/**
 * Defines the application configuration
 */
final class Config
{
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
        // Configure this app to use Aphiria components
        (new AphiriaComponentBuilder($this->container))
            ->withExceptionHandlers($this->appBuilder)
            ->withExceptionLogLevelFactories($this->appBuilder)
            ->withExceptionResponseFactories($this->appBuilder)
            ->withEncoderComponent($this->appBuilder)
            ->withRoutingComponent($this->appBuilder)
            ->withRoutingAnnotations($this->appBuilder)
            ->withValidationComponent($this->appBuilder)
            ->withValidationAnnotations($this->appBuilder)
            ->withConsoleAnnotations($this->appBuilder);

        // Register some global bootstrappers
        $this->appBuilder->withBootstrappers(fn () => [
            new SerializerBootstrapper,
            new ValidationBootstrapper,
            new DependencyInjectionBootstrapper,
            new ExceptionHandlerBootstrapper,
            new LoggerBootstrapper,
            new ContentNegotiatorBootstrapper,
            new RoutingBootstrapper,
            new CommandBootstrapper
        ]);

        // Register any modules
        $this->appBuilder->withModule(new UserModuleBuilder($this->container));
    }
}
